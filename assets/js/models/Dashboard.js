
App.service("Dashboard", /*@ngInject*/ function($http, Cache) {
    var BASE_URL = "/dashboard/";

    Cache.intervalClear("^" + BASE_URL, 60);

    function clearCache() {
        Cache.clear("^" + BASE_URL);
    }

    function updateDashboard(dashboardId, payload) {
        var url = BASE_URL + `${dashboardId}/`;

        clearCache();

        return $http.put(url, payload);
    }

    return {
        getCachedMetadata () {
            return $http.get(BASE_URL + "metadata/", {cache:Cache});
        },

        getDashboards () {
            return $http.get(BASE_URL, {cache:Cache});
        },

        add (payload) {
            clearCache();

            return $http.post(BASE_URL, payload);
        },

        deleteDashboard (dashboardId) {
            clearCache();

            return $http.delete(BASE_URL + `${dashboardId}/`);
        },

        saveOrder (dashboardId, order) {
            return updateDashboard(dashboardId, {
                order: order.join(',')
            });
        },

        saveDashboard (dashboard) {
            return updateDashboard(dashboard.dashboardId, {
                name: dashboard.name
            });
        },

        deleteWidget (dashboardId, widgetId) {
            clearCache();

            return $http.delete(BASE_URL + `${dashboardId}/${widgetId}/`);
        },

        updateWidget (dashboardId, widget) {
            clearCache();

            var url = BASE_URL + `widget/${dashboardId}/${widget.id}/`;
            return $http.put(url, widget);
        },

        updateDashboard
    };
});
