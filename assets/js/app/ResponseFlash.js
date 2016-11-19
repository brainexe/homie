
// show error messages from backend as flash
App.run(/*@ngInject*/ ($httpProvider, $location, UserManagement, Flash, _) => {
    $httpProvider.defaults.transformResponse.push((response, headers, code) => {
        if (code === 502) {
            Flash.addFlash(_('Could not reach server'), Flash.DANGER);
            return response;
        }

        let type = headers('X-Flash-Type');
        if (type) {
            Flash.addFlash(headers('X-Flash-Message'), type);
            if (type === Flash.DANGER) {
                response.status = 500;
            }
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
