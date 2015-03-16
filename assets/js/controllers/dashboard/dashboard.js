
App.Dashboard = {};

App.ng.controller('DashboardController', ['$scope', '$modal', function($scope, $modal) {
	App.Dashboard.$scope = $scope; // todo scope needed?

    $scope.editMode = false;

	$.get('/dashboard/', function(data) {
        var selectedId = Object.keys(data.dashboards)[0];

		$scope.dashboards = data.dashboards;
		$scope.widgets    = data.widgets;

        if (selectedId) {
            $scope.dashboard = data.dashboards[selectedId]
        }

        $scope.$apply();
	});

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
                }
            }
		});
	};

    $scope.deleteDashboard = function(dashboardId) {
        var payload = {
            dashboard_id: dashboardId
        };

        $.post('/dashboard/delete/', payload, function() {
            delete $scope.dashboards[dashboardId];
            $scope.$apply();
        });
    };

    $scope.saveDashboard = function(dashboard) {
        var payload = {
            dashboard_id: dashboard.dashboardId,
            payload: {
                name: dashboard.name
            }
        };

        $.post('/dashboard/update/', payload, function(data) {
            $scope.dashboard = data;
            $scope.$apply();
        });
    };

	/**
     * @param dashboard_id
     * @param {Number} widget_id
	 */
	$scope.deleteWidget = function(dashboard_id, widget_id) {
		var payload = {
            dashboard_id: dashboard_id,
			widget_id:    widget_id
		};

		$.post('/dashboard/widget/delete/', payload, function(data) {
            $scope.dashboard = data;
			$scope.$apply();
		});

		return false;
	}
}]);
