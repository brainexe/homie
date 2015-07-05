
App.controller('SensorController', ['$scope', '$modal', 'SensorGraph', function ($scope, $modal, SensorGraph) {
    $scope.sensors         = {};
    $scope.activeSensorIds = '';
    $scope.currentFrom     = 0;
    $scope.fromIntervals   = {};
    $scope.types           = {};

    $scope.openModal = function () {
        $modal.open({
            templateUrl: asset('/templates/new_sensor.html'),
            controller: 'NewSensorController'
        });
    };

    $scope.editModal = function () {
        $modal.open({
            templateUrl: '/templates/admin/sensors.html',
            controller : 'AdminSensorsController',
            windowClass: 'dialog_1000'
        });
    };

    var element = document.getElementById("chart_container");
    SensorGraph.init($scope, element, 500, '0');
}]);
