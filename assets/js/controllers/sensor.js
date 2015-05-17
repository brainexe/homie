
App.ng.controller('SensorController', ['$scope', '$modal', function($scope, $modal) {
	$scope.sensors          = {};
	$scope.activeSensorIds  = '';
	$scope.currentFrom      = 0;
	$scope.fromIntervals    = {}; // todo sorting in angular is fuzzy
	$scope.availableSensors = {};

    $scope.openModal = function() {
        $modal.open({
            templateUrl: asset('/templates/new_sensor.html'),
            controller: 'NewSensorController'
        });
    };

	$.get('/sensors/load/0', function(data) {
		$scope.sensors         = data.sensors;
		$scope.activeSensorIds = data.activeSensorIds;
		$scope.currentFrom     = data.currentFrom;
		$scope.fromIntervals   = data.fromIntervals;
		$scope.availableSensors = data.availableSensors;

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

		var yAxis = new Rickshaw.Graph.Axis.Y({
			graph: $scope.graph,
			orientation: 'left',
			tickFormat: Rickshaw.Fixtures.Number.formatKMBT,
			element: document.getElementById('yAxis')
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

	/**
	 * @param {Number} sensorId
	 * @returns {boolean}
	 */
	$scope.isSensorActive = function(sensorId) {
		return $scope.activeSensorIds && $scope.activeSensorIds.indexOf(~~sensorId) > -1;
	};

	/**
	 * @param {Number} sensorId
	 * @param {Number} from
	 */
	$scope.sensorView = function(sensorId, from) {
		sensorId = ~~sensorId;
		$scope.currentFrom = from = from || $scope.currentFrom;

		if (sensorId) {
			if ($scope.isSensorActive(sensorId)) {
				var index = $scope.activeSensorIds.indexOf(sensorId);
				$scope.activeSensorIds.splice(index, 1);
			} else {
				$scope.activeSensorIds.push(sensorId);
			}
		}

		var activeIds = $scope.activeSensorIds.join(':') || "0";
		var url = "/sensors/load/{0}?from={1}".format(activeIds, $scope.currentFrom);
		$.get(url, function(data){
			updateGraph(data.json);
		});

		return false;
	};

    $scope.editModal = function() {
        $modal.open({
            templateUrl: asset('/templates/admin/sensors.html'),
            controller: 'AdminSensorsController'
        });
    };

	/**
	 * @param sensorValues
	 */
	function updateGraph(sensorValues) {
		var oldActive = $scope.graph.series.active;
		sensorValues.active = oldActive;
		$scope.graph.series = sensorValues;
		$scope.graph.update();

		var legend = document.querySelector('#legend');
		legend.innerHTML = '';
		new Rickshaw.Graph.Legend( {
			element: legend,
			graph: $scope.graph
		} );
	}
}]);
