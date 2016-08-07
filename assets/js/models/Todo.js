
App.service('Todo', /*@ngInject*/ function($http) {
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
            return $http.post('/todo/{0}/'.format(itemId), {});
        },

        edit: function(data) {
            return $http.put('/todo/', {
                id: data.todoId,
                changes: data
            });
        }
    }
});
