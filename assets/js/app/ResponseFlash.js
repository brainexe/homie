
// show backend messages as flash
App.run(/*@ngInject*/ ($httpProvider, Flash) => {
    $httpProvider.defaults.transformResponse.push((response, headers) => {
        if (headers('X-Flash-Type')) {
            Flash.addFlash(headers('X-Flash-Message'), headers('X-Flash-Type'));
        }

        return response;
    });
});
