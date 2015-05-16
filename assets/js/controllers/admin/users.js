
App.ng.controller('AdminUsersController', ['$scope', '$http', function ($scope, $http) {

    $scope.rights = [];
    $scope.users  = [];

    $http.get('/admin/users/').success(function(data) {
        $scope.rights = data.rights;
        $scope.users  = data.users;
    });

    $scope.save = function(user) {
        $http.post('/admin/users/edit/', user).success(function(newUser) {
            user.edit = false;
        });
    };

    $scope.newPassword = function(user) {
        var password = prompt('Password');
        user.password = password;
        $http.post('/admin/users/edit/', user);
    }
}]);
