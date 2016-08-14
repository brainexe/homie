
App.service('Flash', /*@ngInject*/ function($rootScope) {
    return {
        SUCCESS: 'success',
        ERROR:   'error',
        INFO:    'info',

        addFlash (message, type) {
            $rootScope.$broadcast('flash', [message, type]);
        }
    };
});
