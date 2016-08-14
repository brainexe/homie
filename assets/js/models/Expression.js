
App.service('Expression', /*@ngInject*/ function($http, Cache) {
    var BASE_URL = '/expressions/';

    function clearCache() {
        Cache.clear('^' + BASE_URL);
    }

    Cache.intervalClear('^' + BASE_URL, 60 * 5);

    return {
        getData (cached) {
            return $http.get(BASE_URL, {
                cache: cached ? Cache : false
            });
        },

        getEvents () {
            return $http.get(BASE_URL + 'events/', {
                cache: Cache
            });
        },

        getFunctions () {
            return $http.get(BASE_URL + 'functions/', {
                cache: Cache
            });
        },

        evaluate (expression, cached) {
            return $http.get(BASE_URL + 'evaluate/', {
                params: {expression: expression},
                cache: cached ? Cache : false
            });
        },

        validate (expression) {
            return $http.get(BASE_URL + 'validate/', {
                params: {expression: expression},
                cache: Cache
            });
        },

        save (expression) {
            clearCache();

            return $http.put(BASE_URL, expression);
        },

        deleteExpression (expressionId) {
            clearCache();

            return $http.delete(`${BASE_URL}${expressionId}/`);
        },

        addCron (cron) {
            clearCache();

            return $http.post('/cron/', cron);
        },

        // todo cache until nextRun > now()
        getNextCronRun (expression) {
            return $http.get('/cron/next/', {
                params: {expression}
            });
        },

        invalidate: clearCache
    };
});
