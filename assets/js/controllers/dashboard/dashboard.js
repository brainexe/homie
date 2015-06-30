
App.controller('DashboardController', ['$scope', '$modal', 'Dashboard', 'WidgetFactory', function($scope, $modal, Dashboard, WidgetFactory) {
    $scope.editMode = false;

    function selectDashboard(dashboard) {
        var order = [];

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

    Dashboard.getData().success(function (data) {
        var selectedId = Object.keys(data.dashboards)[0];

        $scope.dashboards = data.dashboards;
        $scope.widgets    = data.widgets;

        if (selectedId) {
            selectDashboard(data.dashboards[selectedId]);
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

    $scope.metadata = function(type, key) {
        for (var i in $scope.widgets) {
            if ($scope.widgets[i].widgetId == type) {
                var widget = $scope.widgets[i];

                if (key) {
                    return widget[key];
                }
                return widget;
            }
        }
        return null;
    };

	$scope.toggleWidget = function(widget, dashboard) {
        var open = widget.open = !widget.open;
        Dashboard.updateWidget(dashboard.dashboardId, widget);
    };

	$scope.selectDashboard = function(dashboard) {
        selectDashboard($scope.dashboards[dashboard.dashboardId]);
    };

    $scope.openModal = function(dashboards) {
        $modal.open({
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
	};

    $scope.deleteDashboard = function(dashboardId) {
        Dashboard.deleteDashboard(dashboardId).success(function() {
            delete $scope.dashboards[dashboardId];
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
            selectDashboard(data); // todo what to show?
		});

		return false;
	}
}]);
