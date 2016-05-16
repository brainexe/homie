
App.service('Nodes', ['$http', 'Cache', function($http, Cache) {
    return {
        getData: function() {
            return $http.get('/nodes/', {cache: Cache});
        },

        add: function(node) {
            Cache.clear('^/nodes/');

            return $http.post('/nodes/', node);
        },

        edit: function (node) {
            Cache.clear('^/nodes/');

            return $http.put('/nodes/{0}/'.format(node.nodeId), node);
        },

        remove: function (node) {
            Cache.clear('^/nodes/');

            return $http.delete('/nodes/{0}/'.format(node.nodeId));
        }
    }
}]);
