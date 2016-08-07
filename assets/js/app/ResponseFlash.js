
// show backend messages as flash
App.run(/*@ngInject*/ function ($httpProvider, $rootScope) {
    $httpProvider.defaults.transformResponse.push(function(response, headers) {
        if (headers('X-Flash-Type')) {
            $rootScope.$broadcast('flash', [headers('X-Flash-Message'), headers('X-Flash-Type')]);
        }

        return response;
    });
});
