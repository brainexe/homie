
App.ng.service('Todo', ['$http', function($http) {
    return {
        getData: function() {
            return $http.get('/todo/');
        },

        assign: function(itemId, userId) {
            return $http.post('/todo/assign/', {
                id:     itemId,
                userId: userId
            });
        },

        add: function(data) {
            return $http.post('/todo/', data);
        },

        deleteItem: function(itemId) {
            return $http.post('/todo/{0}/'.format(todoId), {});
        },

        edit: function(itemId, data) {
            $http.put('/todo/', {
                id: itemId,
                changes: data
            });
        }
    }
}]);
