
App.service('Expression', ['$http', 'Cache', function($http, Cache) {
    Cache.intervalClear('^/expression/', 60);

    return {
        getData: function(cached) {
            return $http.get('/expressions/', {
                cache: cached ? Cache : false
            });
        },

        getEvents: function() {
            return $http.get('/expressions/events/', {
                cache: Cache
            });
        },

        getFunctions: function() {
            return $http.get('/expressions/functions/', {
                cache: Cache
            });
        },

        evaluate: function(expression, cached) {
            return $http.get('/expressions/evaluate/', {
                params: {expression: expression},
                cache: cached ? Cache : false
            });
        },

        save: function(expression) {
            return $http.put('/expressions/', expression);
        },

        deleteExpression: function(expressionId) {
            return  $http.delete('/expressions/{0}/'.format(expressionId));
        },

        addCron: function(cron) {
            return $http.post('/cron/', cron);
        },

        // todo cache until nextRun > now()
        getNextCronRun: function(expression) {
            return $http.get('/cron/next/', {
                params: {expression: expression}
            });
        },

        invalidate: function() {
            return Cache.clear('^/expression/');
        }
    }
}]);
