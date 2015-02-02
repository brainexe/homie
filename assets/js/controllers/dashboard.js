
App.Widgets = {
	time: {
		interval: 1000,
		title: 'Current Time',
		render: function ($scope, widget) {
			$scope.content = new Date().toString();
		}
	},

	sensor: {
		interval: 60 * 5 * 1000,
		render: function ($scope, widget) {
			$scope.content = widget.sensor_id + Math.random();
			$.get('/sensors/value/', {sensor_id: widget.sensor_id}, function(sensor_data) {
				$scope.title = "{0} ({1})".format(sensor_data.sensor.name, sensor_data.sensor.type);
				$scope.content = sensor_data.sensor.name + ": " + sensor_data.sensor_value_formatted;
				$scope.$apply();
			});
		}
	}
};

App.ng.controller('NewWidgetController', ['$scope', '$modalInstance', 'widgets', function ($scope, $modalInstance, widgets) {
	$scope.widgets = widgets;
	$scope.payload = {};

	$scope.addWidget = function(widget) {
		var payload = {
			type: widget.widgetId,
			payload: $scope.payload
		};

		$.post('/dashboard/add/', payload, function(data) {
			$scope.$parent.dashboard = data;
			$scope.dashboard = data;
			$scope.$apply();
		});
		$modalInstance.close();
	};

	$scope.close = function() {
		$modalInstance.close();
	}
}]);

App.ng.controller('DashboardController', ['$scope', '$modal', function ($scope, $modal) {
	$.get('/dashboard/', function(data) {
		$scope.dashboard = data.dashboard;
		$scope.widgets   = data.widgets;
		$scope.$apply();
	});

	$scope.openModal = function() {
		var modalInstance = $modal.open({
			templateUrl: asset('/templates/widgets/new_widget.html'),
			controller: 'NewWidgetController',
			resolve: {
				widgets: function() {
					return $scope.widgets;
				}
			}
		});
	};

	/**
	 * @param {Number} widget_id
	 */
	$scope.deleteWidget = function(widget_id) {
		var payload = {
			widget_id: widget_id
		};

		$.post('/dashboard/delete/', payload, function(data) {
			$scope.dashboard = data;
			$scope.$apply();
		});

		return false;
	}
}]);

App.ng.controller('WidgetController', ['$scope', function ($scope) {
	var widget_payload = $scope.$parent.widget;

	var widget_meta = App.Widgets[widget_payload.type];

	$scope.title = widget_meta.title || widget_payload.type;

	function update() {
		widget_meta.render($scope, widget_payload);
	}
	update();

	if (widget_meta.interval) {
		window.setInterval(function() {
				update();
				$scope.$apply();
			}, widget_meta.interval
		);
	}
}]);
