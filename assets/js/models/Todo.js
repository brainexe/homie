
App.service('Todo', /*@ngInject*/ function($http) {
    return {
        getData () {
            return $http.get('/todo/');
        },

        assign (itemId, userId) {
            return $http.post('/todo/assign/', {
                id:     itemId,//todo id => itemId ?!
                userId: userId
            });
        },

        add (data) {
            return $http.post('/todo/', data);
        },

        deleteItem (itemId) {
            return $http.post(`/todo/${itemId}/`, {});
        },

        edit (data) {
            return $http.put('/todo/', {
                id: data.todoId,
                changes: data
            });
        }
    };
});
