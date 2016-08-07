
App.service('Dashboard', /*@ngInject*/ function($http, Cache) {
    const BASE_URL = '/dashboard/';

    Cache.intervalClear('^'+BASE_URL, 60);

    function clearCache() {
        Cache.clear('^'+BASE_URL);
    }

    function updateDashboard(dashboardId, payload) {
        var url = BASE_URL + '{0}/'.format(dashboardId);

        clearCache();

        return $http.put(url, payload);
    }

    return {
        getCachedMetadata: function() {
            return $http.get(BASE_URL + 'metadata/', {cache:Cache});
        },

        getDashboards: function() {
            return $http.get(BASE_URL, {cache:Cache});
        },

        add: function(payload) {
            clearCache();

            return $http.post(BASE_URL, payload);
        },

        deleteDashboard: function(dashboardId) {
            clearCache();

            return $http.delete(BASE_URL + '{0}/'.format(dashboardId));
        },

        saveOrder: function(dashboardId, order) {
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
            clearCache();

            return $http.delete(BASE_URL + '{0}/{1}/'.format(dashboardId, widgetId))
        },

        updateWidget: function(dashboardId, widget) {
            clearCache();

            var url = BASE_URL + 'widget/{0}/{1}/'.format(dashboardId, widget.id);
            return $http.put(url, widget)
        },

        updateDashboard: updateDashboard
    }
});
