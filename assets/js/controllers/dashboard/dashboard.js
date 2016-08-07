
App.controller('DashboardController', /*@ngInject*/ function($scope, $uibModal, $q, Dashboard, UserManagementSettings, lodash) {
    $scope.editMode = false;

    function selectDashboard(dashboard, notSaveOption) {
        if (!notSaveOption) {
            Settings.set('selectedDashboardId', dashboard.dashboardId);
        }

        if (dashboard.order) {
            var order = dashboard.order.split(',').map(Number);

            dashboard.widgets.sort(function(a, b) {
                var indexA = order.indexOf(a.id);
                var indexB = order.indexOf(b.id);
                return indexA > indexB;
            });
        }

        $scope.dashboard = dashboard;
    }

    $q.all([
        Dashboard.getCachedMetadata(),
        Dashboard.getDashboards(),
        UserManagementSettings.getAll()
    ]).then(function(data) {
        var metadata     = data[0].data,
            dashboards   = data[1].data,
            settings     = data[2].data,
            dashboardIds = Object.keys(dashboards.dashboards).map(Number),
            selectedId;

        $scope.dashboards = dashboards.dashboards;
        $scope.widgets    = metadata.widgets;

        selectedId = ~~settings.selectedDashboardId;

        if (!selectedId || dashboardIds.indexOf(selectedId) == -1) {
            selectedId = dashboardIds[0];
        }

        if (selectedId) {
            selectDashboard(dashboards.dashboards[selectedId], true);
        }
    });

    $scope.dragControlListeners = {
        orderChanged: function (event) {
            var items = event.dest.sortableScope.modelValue;
            var order = items.map(function (item) {
                return item.id;
            });

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
        var modal = $uibModal.open({
			templateUrl: '/templates/widgets/new.html',
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
            selectDashboard(_.first($scope.dashboards));
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
        var modal = $uibModal.open({
            templateUrl: '/templates/widgets/edit.html',
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
            selectDashboard(data, true);
        });
	}
});
