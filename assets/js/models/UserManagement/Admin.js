
App.ng.service('UserManagement.Admin', ['$http', function($http) {
    return {
        getUsers: function() {
            return $http.get('/admin/users/');
        },

        edit: function(user) {
            return $http.put('/admin/users/', user);
        }
    };
}]);
