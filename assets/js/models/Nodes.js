
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

            let newNode = angular.copy(node);
            newNode.options = JSON.stringify(node.options);

            return $http.post('/nodes/', newNode);
        },

        edit (node) {
            clearCache();

            let newNode = angular.copy(node);
            newNode.options = JSON.stringify(node.options);

            return $http.put(`/nodes/${node.nodeId}/`, newNode);
        },

        remove (node) {
            clearCache();
            return $http.delete(`/nodes/${node.nodeId}/`);
        }
    };
});
