
App.controller('SensorController', ['$scope', '$uibModal', 'SensorGraph', 'UserManagement.Settings', function ($scope, uibModal, SensorGraph, Settings) {
    $scope.sensors         = {};
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

    var element = document.getElementsByClassName("chart_container")[0];
    SensorGraph.init($scope, element, 500, [0]);
}]);
