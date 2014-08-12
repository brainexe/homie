
App.ng.controller('SensorController', ['$scope', function($scope) {
	$scope.sensors = {};
	$scope.active_sensor_ids = '';
	$scope.current_from = 0;
	$scope.from_intervals = {};
	$scope.available_sensors = {};

	$.get('/sensors/load/0', function(data) {
		$scope.sensors = data.sensors;
		$scope.active_sensor_ids = data.active_sensor_ids;
		$scope.current_from = data.current_from;
		$scope.from_intervals = data.from_intervals;
		$scope.available_sensors = data.available_sensors;

		require(['sensor'], function(){
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
			var legend = document.querySelector('#legend');

			new Rickshaw.Graph.HoverDetail({
				graph: $scope.graph
			});


			$scope.$apply();
		});
	});

	/**
	 * @param {Number} sensor_id
	 * @returns {boolean}
	 */
	$scope.isSensorActive = function(sensor_id) {
		return $scope.active_sensor_ids && $scope.active_sensor_ids.indexOf(sensor_id) > -1;
	};

	/**
	 * @param {Number} sensor_id
	 * @param {Number} from
	 */
	$scope.sensorView = function(sensor_id, from) {
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
		$.get("/sensors/load/{0}?from={1}".format(active_ids, $scope.current_from), function(data){
			updateGraph(data.json);
		});

		return false;
	};

	/**
	 * @param sensor_values
	 */
	function updateGraph(sensor_values) {
		var old_active = $scope.graph.series.active;
		sensor_values.active = old_active;
		$scope.graph.series = sensor_values;
		$scope.graph.update();
	}
}]);
