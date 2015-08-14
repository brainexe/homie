
App.service('Dashboard', ['$http', 'Cache', function($http, Cache) {
    Cache.intervalClear('^/dashboard/', 60);

    function updateDashboard(dashboardId, payload) {
        var url = '/dashboard/{0}/'.format(dashboardId);

        return $http.put(url, payload);
    }

    return {
        getCachedMetadata: function() {
            return $http.get('/dashboard/metadata/', {cache:Cache});
        },

        getDashboards: function() {
            return $http.get('/dashboard/', {cache:Cache});
        },

        add: function(payload) {
            Cache.clear('^/dashboard/.*');
            return $http.post('/dashboard/', payload);
        },

        deleteDashboard: function(dashboardId) {
            Cache.clear('^/dashboard/.*');
            return $http.delete('/dashboard/{0}/'.format(dashboardId));
        },

        saveOrder: function(dashboardId, order) {
            Cache.clear('^/dashboard/.*');
            return updateDashboard(dashboardId, {
                order: order.join(',')
            });
        },

        saveDashboard: function(dashboard) {
            return updateDashboard(dashboard.dashboardId, {
                name: dashboard.name
            });
        },

        deleteWidget: function(dashboardId, widgetId) {
            Cache.clear('^/dashboard/.*');
            return $http.delete('/dashboard/{0}/{1}/'.format(dashboardId, widgetId))
        },

        updateWidget: function(dashboardId, widget) {
            Cache.clear('^/dashboard/.*');

            var url = '/dashboard/widget/{0}/{1}/'.format(dashboardId, widget.id);
            return $http.put(url, widget)
        },

        updateDashboard: updateDashboard
    }
}]);
