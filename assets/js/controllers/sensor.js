
App.controller('SensorController', ['$scope', '$uibModal', 'SensorGraph', function ($scope, uibModal, SensorGraph) {
    $scope.sensors         = {};
    $scope.activeSensorIds = '';
    $scope.currentFrom     = 0;
    $scope.fromIntervals   = {};
    $scope.types           = {};

    $scope.editModal = function () {
        uibModal.open({
            templateUrl: '/templates/sensor/edit.html',
            controller : 'EditSensorsController',
            windowClass: 'dialog_1200'
        });
    };

    $scope.addModal = function () {
        uibModal.open({
            templateUrl: '/templates/sensor/add.html',
            controller : 'AddSensorsController',
            windowClass: 'dialog_1200'
        });
    };

    var element = document.getElementsByClassName("chart_container")[0];
    SensorGraph.init($scope, element, 500, '0');
}]);
