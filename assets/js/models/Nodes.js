
App.service('Nodes', ['$http', 'Cache', function($http, Cache) {
    return {
        getData: function() {
            return $http.get('/nodes/');
        },

        add: function(node) {
            return $http.post('/nodes/', node);
        },

        edit: function (node) {
            return $http.put('/nodes/{0}/'.format(node.nodeId), node);
        },

        remove: function (node) {
            return $http.delete('/nodes/{0}/'.format(node.nodeId));
        }
    }
}]);
