
App.controller('DisplaysController', ['$scope', 'Displays', function ($scope, Displays) {

    $scope.screens   = {};
    $scope.newScreen = {content: ['']};

    Displays.getData().success(function (data) {
        $scope.screens = data.screens;

        console.log(data);
    });

    $scope.add = function(display) {
        Displays.add(display).success(function(newScreen) {
            $scope.screens[newScreen.displayId] = newScreen;
        })
    }
}]);
