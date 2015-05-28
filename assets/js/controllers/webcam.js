App.ng.controller('WebcamController', ['$scope', 'Webcam', function ($scope, Webcam) {
    $scope.shots = [];

    Webcam.getData().success(function (data) {
        $scope.shots = data.shots;
    });

    $scope.takeShot = function () {
        Webcam.takeShot().success(function() {
            App.Layout.$scope.addFlash('Cheese...', 'info');
        });
    };

    $scope.removeShot = function (index) {
        var shot = $scope.shots[index];

        shot.deleting = true;
        Webcam.remove(shot.webPath).success(function () {
            $scope.shots.slice(index, 1);
        });
    };

    $scope.$on('webcam.took_photo', function (data) {
        $scope.shots.push(data);
        $scope.$apply();
    });
}]);
