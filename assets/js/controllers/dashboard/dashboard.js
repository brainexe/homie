
App.controller('DashboardController', ['$scope', '$modal', 'Dashboard', 'WidgetFactory', function($scope, $modal, Dashboard, WidgetFactory) {
    $scope.editMode = false;

    Dashboard.getData().success(function (data) {
        var selectedId = Object.keys(data.dashboards)[0];

        $scope.dashboards = data.dashboards;
        $scope.widgets = data.widgets;

        if (selectedId) {
            $scope.dashboard = data.dashboards[selectedId]
        }
    });

    $scope.dragControlListeners = {
        //accept: function (sourceItemHandleScope, destSortableScope) {
        //    return true;
        //},
        //itemMoved: function (event) {
        //},
        //orderChanged: function (event) {
        //},
        containment: '#sortable-container'
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

	$scope.selectDashboard = function(dashboard) {
        $scope.dashboard = $scope.dashboards[dashboard.dashboardId];
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
            $scope.dashboard = data;
        });
    };

	/**
     * @param {Number} dashboardId
     * @param {Number} widgetId
	 */
	$scope.deleteWidget = function(dashboardId, widgetId) {
		Dashboard.deleteWidget(dashboardId, widgetId).success(function(data) {
            $scope.dashboard = data;
		});

		return false;
	}
}]);
