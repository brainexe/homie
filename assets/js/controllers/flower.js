
App.ng.controller('FlowerController', ['$scope', 'Flower', function ($scope, Flower) {
    Flower.getData().success(function (data) {
        $scope.humidity     = data.humidity;
        $scope.waterEnabled = data.humidity <= 50;
    });

    $scope.water = function () {
        $scope.waterEnabled = false;

        Flower.water().success(function (data) {
            $scope.waterEnabled = true;
        });
    };
}]);
