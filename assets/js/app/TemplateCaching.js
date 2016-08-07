
App.config(/*@ngInject*/ function ($routeProvider, $httpProvider, $provide) {
    // needed for translated routes
    $provide.factory('$routeProvider', function () {
        return $routeProvider;
    });
    $provide.factory('$httpProvider', function () {
        return $httpProvider;
    });

    $httpProvider.interceptors.push(/*@ngInject*/ function($templateCache, Cache) {
        return {
            request: function(request) {
                var url = request.url;
                var cached;
                if (url.match(/\.html/)) {
                    if ((cached = $templateCache.get(url)) && !Cache.get(url)) {
                        Cache.put(url, cached, {maxAge: 86400});
                    }
                    request.cacheKey = url;
                    request.cache = Cache;
                }

                return request;
            },
            response: function(response) {
                if (response.config.cacheKey && !Cache.get(response.config.cacheKey)) {
                    Cache.put(
                        response.config.cacheKey,
                        response.data,
                        {maxAge: 86400}
                    );
                }
                return response;
            }
        };
    });
});
