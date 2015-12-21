
App.service('UserManagement.Avatar', ['$http', 'Cache', function($http, Cache) {
    return {
        getList: function() {
            return $http.get('/user/avatar/', {cache:Cache});
        },

        set: function(avatar) {
            return $http.post('/user/avatar/{0}/'.format(avatar));
        }
    };
}]);
