
App.service('Expression', ['$http', function($http) {
    return {
        getData: function() {
            return $http.get('/expressions/');
        },

        save: function(expression) {
            return $http.put('/expressions/', expression);
        },

        deleteExpression: function(expressionId) {
            return  $http.delete('/expressions/', {expressionId:expressionId});
        },

        addCron: function(cron) {
            return $http.post('/expressions/cron/', cron);
        }
    }
}]);
