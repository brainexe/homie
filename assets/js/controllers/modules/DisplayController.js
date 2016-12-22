
App.controller('DisplaysController', /*@ngInject*/ function ($scope, Displays, Nodes) {
    $scope.editMode = false;
    $scope.screens  = {};
    $scope.currentScreen = {
        content: ["''", "''", "''", "''"],
        lines:   4,
        columns: 10
    };

    Nodes.getData().then(function(data) {
        $scope.nodes = data.data.nodes;
    });

    Displays.getData().then(function (data) {
        $scope.screens = data.data.screens;
    });

    $scope.save = function(display) {
        var result;
        if (display.displayId) {
            result = Displays.update(display);
        } else {
            result = Displays.add(display);
        }

        result.then(function(screen) {
            $scope.screens[screen.data.displayId] = screen.data;
        });
    };

    $scope.setScreen = function(screen) {
        $scope.editMode = true;
        $scope.currentScreen = screen;
    };

    $scope.refreshScreen = function(screen) {
        Displays.redraw(screen.displayId).then(function() {
        });
    };

    $scope.deleteScreen = function(screen) {
        Displays.delete(screen.displayId).then(function() {
        });
    };
});
