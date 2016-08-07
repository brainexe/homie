
App.service('UserManagementTOTP', /*@ngInject*/ function($http) {
    const BASE_URL = '/one_time_password/';

    return {
        getData: function () {
            return $http.get(BASE_URL);
        },

        request: function () {
            return $http.post(BASE_URL + 'request/', {});
        },

        deleteToken: function () {
            return $http.delete(BASE_URL);
        },

        needsToken: function (username) {
            return $http.get(
                '/login/needsOneTimeToken',
                {
                    params: {username: username}
                }
            );
        },

        sendMail: function (username) {
            return $http.post(BASE_URL + 'mail/', {user_name: username});
        }
    };
});
