
App.controller('DisplaysController', /*@ngInject*/ function ($scope, Displays, Nodes) {
    $scope.editMode = false;
    $scope.screens  = {};
    $scope.currentScreen = {
        content: ["''", "''", "''", "''"],
        lines:   4,
        columns: 10
    };

    Nodes.getData().success(function(data) {
        $scope.nodes = data.nodes;
    });

    Displays.getData().success(function (data) {
        $scope.screens = data.screens;
    });

    $scope.save = function(display) {
        var result;
        if (display.displayId) {
            result = Displays.update(display);
        } else {
            result = Displays.add(display);
        }

        result.success(function(screen) {
            $scope.screens[screen.displayId] = screen;
        });
    };

    $scope.setScreen = function(screen) {
        $scope.editMode = true;
        $scope.currentScreen = screen;
    };

    $scope.refreshScreen = function(screen) {
        Displays.redraw(screen.displayId).success(function() {
        });
    };

    $scope.deleteScreen = function(screen) {
        Displays.delete(screen.displayId).success(function() {
        });
    };
});
