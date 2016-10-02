
App.service('Flash', /*@ngInject*/ function($rootScope) {
    return {
        SUCCESS: 'success',
        DANGER:  'danger',
        INFO:    'info',

        addFlash (message, type) {
            $rootScope.$broadcast('flash', [message, type]);
        }
    };
});
