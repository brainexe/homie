
var templates = new App.TemplateLoader();
App.Dashboard = {};

App.Widgets = {
	time: {
		interval: 1000,
		title: _('Current Time'),
		render: function ($scope, widget) {
			$scope.setContent($scope, new Date().toString());
		}
	},

	sensor: {
		interval: 60 * 5 * 1000,
		render: function ($scope, widget) {
			$.get('/sensors/value/', {sensor_id: widget.sensor_id}, function(sensor_data) {
				$scope.title = "{0} ({1})".format(sensor_data.sensor.name, sensor_data.sensor.type);
				$scope.setContent($scope, sensor_data.sensor.name + ": " + sensor_data.sensor_value_formatted);
				$scope.$apply();
			});
		}
	},

	radio: {
		render: function ($scope, widget) {
			var template = templates.load(assert('/templates/widgets/radio.html'));
			var radios = App.Radios.loadAll();
			Promise.all([radios, template]).then(function(values) {
				var radio = values[0][widget.radioId];
				if (radio) {
					$scope.title = radio.name;
				}
				$scope.setContent($scope, values[1]);
				$scope.$apply();
			});
		}
	},
	egg_timer: {
		title: _('Egg Timer'),
		render: function ($scope, widget) {
			templates
				.load(assert('/templates/widgets/egg_timer.html'))
				.then(function(html) {
					$scope.setContent($scope, html);
					$scope.$apply();
				});
		}
	}
};

App.ng.controller('NewWidgetController', ['$scope', '$modalInstance', 'widgets', function($scope, $modalInstance, widgets) {
	$scope.widgets = widgets;
	$scope.payload = {};

	$scope.addWidget = function(widget) {
		var payload = {
			type: widget.widgetId,
			payload: $scope.payload
		};

		$.post('/dashboard/add/', payload, function(data) {
			App.Dashboard.$scope.dashboard = data;
			App.Dashboard.$scope.$apply();
		});
		$modalInstance.close();
	};

	$scope.close = function() {
		$modalInstance.close();
	}
}]);

App.ng.controller('DashboardController', ['$scope', '$modal', function($scope, $modal) {
	App.Dashboard.$scope = $scope;

	$.get('/dashboard/', function(data) {
		$scope.dashboard = data.dashboard;
		$scope.widgets   = data.widgets;
		$scope.$apply();
	});

	$scope.openModal = function() {
		var modalInstance = $modal.open({
			templateUrl: asset('/templates/widgets/new.html'),
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

App.ng.controller('WidgetController', ['$scope', '$sce', function ($scope, $sce) {
	var widget_payload = $scope.$parent.widget;

	var widget_meta = App.Widgets[widget_payload.type];
	// todo use name from widgets definition
	$scope.title = widget_meta.title || widget_payload.name;

	$scope.setContent = function(scope, html) {
		scope.content = $sce.trustAsHtml(html);
	};

	function update() {
		widget_meta.render($scope, widget_payload);
	}
	update();

	if (widget_meta.interval) {
		window.setInterval(function() {
			update();
			$scope.$apply();
		}, widget_meta.interval);
	}
}]);
