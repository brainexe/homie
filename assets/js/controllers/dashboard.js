
App.Widgets = {
	time: {
		interval: 1000,
		title: 'Current Time',
		render: function ($scope, widget) {
			$scope.content = new Date().toString();
		}
	},

	sensor: {
		interval: 30000,
		render: function ($scope, widget) {
			$scope.content = widget.sensor_id + Math.random();
			$.get('/sensors/value/', {sensor_id: widget.sensor_id}, function(sensor_data) {
				$scope.title = "{0} ({1})".format(sensor_data.sensor.name, sensor_data.sensor.type);
				$scope.content = sensor_data.sensor.name + ": " + sensor_data.sensor_value_formatted;
				$scope.$apply();
			});
		}
	},
};

App.ng.controller('DashboardController', ['$scope', function ($scope) {
	$.get('/dashboard/', function(data) {
		$scope.dashboard = data.dashboard;
		$scope.$apply();
	});

	$scope.addWidget = function() {
		//TODO
	};
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