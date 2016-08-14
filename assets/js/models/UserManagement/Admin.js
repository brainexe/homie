
App.service('UserManagementAdmin', /*@ngInject*/ function($http) {
    return {
        getUsers () {
            return $http.get('/admin/users/');
        },

        edit (user) {
            return $http.put('/admin/users/', user);
        },

        delete (user) {
            return $http.delete(`/admin/users/${user.userId}/`);
        }
    };
});
