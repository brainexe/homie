
App.Dashboard = {};

App.ng.controller('DashboardController', ['$scope', '$modal', function($scope, $modal) {
	App.Dashboard.$scope = $scope; // todo scope needed?

    $scope.editMode = false;

	$.get('/dashboard/', function(data) {
		$scope.dashboards = data.dashboards;
		$scope.widgets    = data.widgets;
		$scope.$apply();
	});

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
            name: dashboard.name
        };

        $.post('/dashboard/update/', payload, function(data) {
            $scope.dashboards[data.dashboardId] = data;
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
			$scope.dashboards[data.dashboardId] = data;
			$scope.$apply();
		});

		return false;
	}
}]);
