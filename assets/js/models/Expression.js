
App.service('Expression', /*@ngInject*/ function($http, Cache) {
    const BASE_URL = '/expressions/';

    function clearCache() {
        Cache.clear('^' + BASE_URL);
    }

    Cache.intervalClear('^' + BASE_URL, 60 * 5);

    return {
        getData: function(cached) {
            return $http.get(BASE_URL, {
                cache: cached ? Cache : false
            });
        },

        getEvents: function() {
            return $http.get(BASE_URL + 'events/', {
                cache: Cache
            });
        },

        getFunctions: function() {
            return $http.get(BASE_URL + 'functions/', {
                cache: Cache
            });
        },

        evaluate: function(expression, cached) {
            return $http.get(BASE_URL + 'evaluate/', {
                params: {expression: expression},
                cache: cached ? Cache : false
            });
        },

        validate: function(expression) {
            return $http.get(BASE_URL + 'validate/', {
                params: {expression: expression},
                cache: Cache
            });
        },

        save: function(expression) {
            clearCache();

            return $http.put(BASE_URL, expression);
        },

        deleteExpression: function(expressionId) {
            clearCache();

            return $http.delete(BASE_URL + '{0}/'.format(expressionId));
        },

        addCron: function(cron) {
            clearCache();

            return $http.post('/cron/', cron);
        },

        // todo cache until nextRun > now()
        getNextCronRun: function(expression) {
            return $http.get('/cron/next/', {
                params: {expression: expression}
            });
        },

        invalidate: clearCache
    }
});
