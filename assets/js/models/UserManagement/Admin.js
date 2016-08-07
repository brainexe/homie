
App.service('UserManagementAdmin', /*@ngInject*/ function($http) {
    return {
        getUsers: function() {
            return $http.get('/admin/users/');
        },

        edit: function(user) {
            return $http.put('/admin/users/', user);
        },

        delete: function(user) {
            return $http.delete('/admin/users/{0}/'.format(user.userId));
        }
    };
});
