
App.controller('UserController', ['$scope', 'UserManagement', 'UserManagement.Avatar', 'controllers', function ($scope, UserManagement, Avatar, controllers) {

    $scope.controllers = controllers().filter(function(controller) {
        return controller.collapsible
    });

    Avatar.getList().success(function(avatars) {
        $scope.avatars = avatars;
    });

    $scope.setAvatar = function (avatar) {
        Avatar.set(avatar).success(function(user) {
            UserManagement.current = user;
        });
    };

    $scope.toggleMenu = function(controller) {
        // TODO
    };
}]);
