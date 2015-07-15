
App.service('Expression', ['$http', 'Cache', function($http, Cache) {
    return {
        getData: function() {
            return $http.get('/expressions/');
        },

        evaluate: function(expression, noCache) {
            return $http.get('/expressions/evaluate/', {
                params: {expression: expression},
                cache: noCache ? false : Cache
            });
        },

        save: function(expression) {
            return $http.put('/expressions/', expression);
        },

        deleteExpression: function(expressionId) {
            return  $http.delete('/expressions/', {expressionId:expressionId});
        },

        deleteEvent: function(eventId) {
            return $http.delete('/stats/event/?job_id={0}'.format(eventId));
        },

        addCron: function(cron) {
            return $http.post('/expressions/cron/', cron);
        }
    }
}]);
