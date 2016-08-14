
App.service('UserManagementAvatar', /*@ngInject*/ function($http, Cache) {
    return {
        getList () {
            return $http.get('/user/avatar/', {cache:Cache});
        },

        set (avatar) {
            return $http.post(`/user/avatar/${avatar}/`, {});
        }
    };
});
