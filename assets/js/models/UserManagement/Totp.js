
App.service('UserManagementTOTP', /*@ngInject*/ function($http) {
    const BASE_URL = '/one_time_password/';

    return {
        getData () {
            return $http.get(BASE_URL);
        },

        request () {
            return $http.post(BASE_URL + 'request/', {});
        },

        deleteToken () {
            return $http.delete(BASE_URL);
        },

        needsToken (username) {
            return $http.get(
                '/login/needsOneTimeToken',
                {
                    params: {username}
                }
            );
        },

        sendMail (username) {
            return $http.post(BASE_URL + 'mail/', {user_name: username});
        }
    };
});
