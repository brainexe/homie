
App.service('Dashboard', ['$http', function($http) {

    function updateDashboard(dashboardId, payload) {
        var url = '/dashboard/{0}/'.format(dashboardId);

        return $http.put(url, payload);
    }

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

        saveOrder: function(dashboardId, order) {
            return updateDashboard(dashboardId, {
                order: order.join(',')
            });
        },

        // todo? save name only?
        saveDashboard: function(dashboard) {
            return updateDashboard(dashboard.dashboardId, {
                name: dashboard.name
            });
        },

        deleteWidget: function(dashboardId, widgetId) {
            return $http.delete('/dashboard/{0}/{1}/'.format(dashboardId, widgetId))
        },

        updateWidget: function(dashboardId, widget) {
            var url = '/dashboard/widget/{0}/{1}/'.format(dashboardId, widget.id);
            return $http.put(url, widget)
        },

        updateDashboard: updateDashboard
    }
}]);
