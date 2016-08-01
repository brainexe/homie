
App.controller('SensorController', ['$scope', '$uibModal', 'SensorGraph', 'UserManagement.Settings', function ($scope, uibModal, SensorGraph, Settings) {
    $scope.sensors         = [];
    $scope.activeSensorIds = '';
    $scope.from            = 0;
    $scope.to              = 0;
    $scope.fromIntervals   = {};
    $scope.types           = {};

    $scope.editModal = function () {
        uibModal.open({
            templateUrl: '/templates/sensor/edit.html',
            controller : 'EditSensorsController'
        });
    };

    $scope.addModal = function () {
        uibModal.open({
            templateUrl: '/templates/sensor/add.html',
            controller : 'AddSensorsController'
        });
    };

    $scope.removeDisabled = function (sensors) {
        return sensors.filter(function(sensor) {
            return sensor.interval >= 1;
        });
    };

    var element = document.querySelector('.chart_container');
    SensorGraph.init($scope, element, 500, [0]);
}]);
