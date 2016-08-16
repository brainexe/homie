
// show backend messages as flash
App.run(/*@ngInject*/ ($httpProvider, $location, UserManagement, Flash) => {
    $httpProvider.defaults.transformResponse.push((response, headers) => {
        if (headers('X-Flash-Type')) {
            Flash.addFlash(headers('X-Flash-Message'), headers('X-Flash-Type'));
        }

        if (headers('X-Error') === 'NotAuthorized') {
            console.log('Force user logout');
            UserManagement.setCurrentUser({});
            $location.path("/login");
            return null;
        }

        return response;
    });
});
