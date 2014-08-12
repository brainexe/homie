
App.Widgets = {
	time: {
		interval: 1000,
		render: function (scope, widget) {
			scope.content = new Date().toString();
			scope.interval = 1000;
			scope.$apply();
		}
	},

	sensor: {
		interval: 30000,
		render: function (scope, widget) {
			scope.content = widget.sensor_id + Math.random();
			$.get('/sensors/value/', {sensor_id: widget.sensor_id}, function(sensor_data) {
				scope.title = "{0} ({1})".format(sensor_data.sensor.name, sensor_data.sensor.type);
				scope.content = sensor_data.sensor.name + ": " + sensor_data.sensor_value_formatted;
				scope.$apply();
			});
		}
	},
};

App.ng.directive('widget', function ($compile) {
	var loader = new App.TemplateLoader('widgets/');

	var linker = function(scope, element, attrs) {
		var widget_payload = scope.widget;
		var widget_meta = App.Widgets[widget_payload.type];

		scope.title = widget_meta.title || widget_payload.type;

		function update() {
			widget_meta.render(scope, widget_payload);
		}
		update();

		if (widget_meta.interval) {
			window.setInterval(update, widget_meta.interval);
		}

		loader.load('widget', function(widgetTemplate) {
			element.html(widgetTemplate).show();
			$compile(element.contents())(scope);
		});
	};

	return {
		restrict: "E",
		rep1ace: true,
		link: linker,
		scope: {
			widget:'='
		}
	};
});

App.ng.controller('DashboardController', ['$scope', '$http', '$templateCache', function ($scope, $http, $templateCache) {

	$scope.tpl = '';

	$http.get('/templates/widgets/widget.html', { cache: $templateCache } )
		.then( function( response ) {

			$scope.tpl = response.data;

			$.get('/dashboard/', function(data) {
				$scope.dashboard = data.dashboard;
				$scope.$apply();
			});
		});

	$scope.addWidget = function() {

	};
}]);