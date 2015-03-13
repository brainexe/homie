
App.Dashboard = {};

App.ng.controller('DashboardController', ['$scope', '$modal', function($scope, $modal) {
	App.Dashboard.$scope = $scope;

	$.get('/dashboard/', function(data) {
		$scope.dashboards = data.dashboards;
		$scope.widgets    = data.widgets;
		$scope.$apply();
	});

	$scope.openModal = function(dashboard) {
        $modal.open({
			templateUrl: asset('/templates/widgets/new.html'),
			controller: 'NewWidgetController',
			resolve: {
				widgets: function() {
					return $scope.widgets;
				},
                dashboard: function () {
                    return dashboard;
                }
            }
		});
	};

	/**
     * @param dashboard_id
     * @param {Number} widget_id
	 */
	$scope.deleteWidget = function(dashboard_id, widget_id) {
		var payload = {
            dashboard_id: dashboard_id,
			widget_id: widget_id
		};

		$.post('/dashboard/delete/', payload, function(data) {
			$scope.dashboard = data;
			$scope.$apply();
		});

		return false;
	}
}]);
