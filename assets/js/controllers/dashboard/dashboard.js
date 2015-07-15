
App.controller('DashboardController', ['$scope', '$modal', '$q', 'Dashboard', 'WidgetFactory', function($scope, $modal, $q, Dashboard, WidgetFactory) {
    $scope.editMode = false;

    function selectDashboard(dashboard) {
        var order = [];
        localStorage['selectedDashboardId'] = dashboard.dashboardId;

        if (dashboard.order) {
            order = dashboard.order.split(',').map(function(id) {
                return ~~id;
            });
        }

        dashboard.widgets.sort(function(a, b) {
            var index_a = order.indexOf(a.id);
            var index_b = order.indexOf(b.id);
            return index_a > index_b;
        });
        $scope.dashboard = dashboard;
    }

    $q.all([
        Dashboard.getCachedMetadata(),
        Dashboard.getDashboards()
    ]).then(function(data) {
        var metadata     = data[0].data,
            dashboards   = data[1].data,
            dashboardIds = Object.keys(dashboards.dashboards),
            selectedId;

        $scope.dashboards = dashboards.dashboards;
        $scope.widgets    = metadata.widgets;

        selectedId = localStorage['selectedDashboardId'];
        if (!selectedId || dashboardIds.indexOf(selectedId) == -1) {
            selectedId = dashboardIds[0];
        }

        if (selectedId) {
            selectDashboard(dashboards.dashboards[selectedId]);
        }
    });

    $scope.dragControlListeners = {
        orderChanged: function (event) {
            var order = [];
            var items = event.dest.sortableScope.modelValue;
            for (var idx in items) {
                order.push(items[idx].id);
            }
            Dashboard.saveOrder($scope.dashboard.dashboardId, order);
        }
    };

    $scope.metadata = function(widget, key) {
        var type = widget.type;

        var metadata = $scope.widgets[type];

        if (key) {
            if (widget[key]) {
                return widget[key];
            }
            return metadata[key];
        }
        return metadata;
    };

	$scope.toggleWidget = function(widget, dashboard) {
        widget.open = !widget.open;
        Dashboard.updateWidget(dashboard.dashboardId, widget);
    };

	$scope.selectDashboard = function(dashboard) {
        selectDashboard($scope.dashboards[dashboard.dashboardId]);
    };

    $scope.openModal = function(dashboards) {
        var modal = $modal.open({
			templateUrl: asset('/templates/widgets/new.html'),
			controller: 'NewWidgetController',
			resolve: {
				widgets: function() {
					return $scope.widgets;
				},
                dashboards: function () {
                    return dashboards;
                },
                currentDashboard: function () {
                    return $scope.dashboard
                }
            }
		});
        modal.result.then(function(data) {
            if (data) {
                $scope.dashboards[data.dashboardId] = data;
                selectDashboard(data);
            }
        });
	};

    $scope.deleteDashboard = function(dashboardId) {
        Dashboard.deleteDashboard(dashboardId).success(function() {
            delete $scope.dashboards[dashboardId];
            // todo what to show?
        });
    };

    $scope.saveDashboard = function(dashboard) {
        Dashboard.saveDashboard(dashboard).success(function(data) {
            selectDashboard(data);
        });
    };

	/**
     * @param {Number} dashboardId
     * @param {Number} widgetId
	 */
	$scope.deleteWidget = function(dashboardId, widgetId) {
		Dashboard.deleteWidget(dashboardId, widgetId).success(function(data) {
            selectDashboard(data);
		});

		return false;
	};

	$scope.editWidget = function(dashboardId, widget) {
        var modal = $modal.open({
            templateUrl: asset('/templates/widgets/edit.html'),
            controller: 'EditWidgetController',
            resolve: {
                widget: function() {
                    return widget;
                },
                dashboardId: function () {
                    return dashboardId;
                }
            }
        });
        modal.result.then(function(data) {
            selectDashboard(data);
        });
	}
}]);
