
App.service('Gpio', /*@ngInject*/ function($http) {
    return {
        getData: (nodeId) => $http.get(`/gpio/${nodeId}/`),

        setDescription (nodeId, pinId, description) {
            return $http.post(
                '/gpio/description/', {pinId, nodeId, description}
            );
        },

        savePin (nodeId, pin, direction, value) {
            var url = `/gpio/set/${nodeId}/${pin}/${direction}/${value}/`;
            return $http.post(url, {});
        }
    };
});
