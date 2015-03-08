
App.ng.controller('SensorController', ['$scope', '$modal', function($scope, $modal) {
	$scope.sensors           = {};
	$scope.active_sensor_ids = '';
	$scope.current_from      = 0;
	$scope.fromIntervals     = {}; // todo sorting in angular is fuzzy
	$scope.available_sensors = {};

	$.get('/sensors/load/0', function(data) {
		$scope.sensors           = data.sensors;
		$scope.active_sensor_ids = data.active_sensor_ids;
		$scope.current_from      = data.current_from;
		$scope.fromIntervals     = data.fromIntervals;
		$scope.available_sensors = data.available_sensors;

		require(['sensor'], function() {
			$scope.graph = new Rickshaw.Graph({
				element: document.getElementById("chart"),
				width: $('.content').width(),
				interpolation: 'basis',
				height: 500,
				min: 'auto',
				renderer: 'line',
				series: data.json
			});

			new Rickshaw.Graph.Axis.Time({ graph: $scope.graph });

			var y_axis = new Rickshaw.Graph.Axis.Y({
				graph: $scope.graph,
				orientation: 'left',
				tickFormat: Rickshaw.Fixtures.Number.formatKMBT,
				element: document.getElementById('y_axis')
			});

			$scope.graph.render();

			new Rickshaw.Graph.HoverDetail({
				graph: $scope.graph
			});

			new Rickshaw.Graph.Legend( {
				element: document.querySelector('#legend'),
				graph: $scope.graph
			} );

			$scope.$apply();
		});
	});

	/**
	 * @param {Number} sensor_id
	 * @returns {boolean}
	 */
	$scope.isSensorActive = function(sensor_id) {
		return $scope.active_sensor_ids && $scope.active_sensor_ids.indexOf(~~sensor_id) > -1;
	};

	/**
	 * @param {Number} sensor_id
	 * @param {Number} from
	 */
	$scope.sensorView = function(sensor_id, from) {
		sensor_id = ~~sensor_id;
		$scope.current_from = from = from || $scope.current_from;

		if (sensor_id) {
			if ($scope.isSensorActive(sensor_id)) {
				var index = $scope.active_sensor_ids.indexOf(sensor_id);
				$scope.active_sensor_ids.splice(index, 1);
			} else {
				$scope.active_sensor_ids.push(sensor_id);
			}
		}

		var active_ids = $scope.active_sensor_ids.join(':') || "0";
		var url = "/sensors/load/{0}?from={1}".format(active_ids, $scope.current_from);
		$.get(url, function(data){
			updateGraph(data.json);
		});

		return false;
	};

    $scope.editModal = function() {
        var modalInstance = $modal.open({
            templateUrl: asset('/templates/admin/sensors.html'),
            controller: 'AdminSensorsController'
        });
    };

	/**
	 * @param sensor_values
	 */
	function updateGraph(sensor_values) {
		var old_active = $scope.graph.series.active;
		sensor_values.active = old_active;
		$scope.graph.series = sensor_values;
		$scope.graph.update();

		var legend = document.querySelector('#legend');
		legend.innerHTML = '';
		new Rickshaw.Graph.Legend( {
			element: legend,
			graph: $scope.graph
		} );
	}
}]);
