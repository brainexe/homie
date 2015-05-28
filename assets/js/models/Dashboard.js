
App.ng.service('Dashboard', ['$http', function($http) {
    return {
        getData: function() {
            return $http.get('/dashboard/');
        },

        add: function(payload) {
            return $http.post('/dashboard/', payload);
        },

        deleteDashboard: function(dashboardId) {
            return $http.delete('/dashboard/{0}/'.format(dashboardId));
        },

        saveDashboard: function(dashboard) {
            var url = '/dashboard/{0}/'.format(dashboard.dashboardId),
                payload = {
                    payload: {
                        name: dashboard.name
                    }
                };

            return $http.put(url, payload);
        },

        deleteWidget: function(dashboardId, widgetId) {
            return $http.delete('/dashboard/{0}/{1}'.format(dashboardId, widgetId))
        }
    }
}]);
