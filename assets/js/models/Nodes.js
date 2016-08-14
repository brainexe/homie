
App.service('Nodes', /*@ngInject*/ function($http, Cache) {
    function clearCache() {
        Cache.clear('^/nodes/');
    }

    return {
        getData () {
            return $http.get('/nodes/', {cache: Cache});
        },

        add (node) {
            clearCache();
            return $http.post('/nodes/', node);
        },

        edit (node) {
            clearCache();
            return $http.put(`/nodes/${node.nodeId}/`, node);
        },

        remove (node) {
            clearCache();
            return $http.delete(`/nodes/${node.nodeId}/`);
        }
    };
});
