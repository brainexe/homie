
App.service('Nodes', /*@ngInject*/ function($http, Cache) {
    function clearCache() {
        Cache.clear('^/nodes/');
    }

    return {
        getData: function() {
            return $http.get('/nodes/', {cache: Cache});
        },

        add: function(node) {
            clearCache();
            return $http.post('/nodes/', node);
        },

        edit: function (node) {
            clearCache();
            return $http.put('/nodes/{0}/'.format(node.nodeId), node);
        },

        remove: function (node) {
            clearCache();
            return $http.delete('/nodes/{0}/'.format(node.nodeId));
        }
    }
});
