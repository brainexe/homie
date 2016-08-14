
App.config(/*@ngInject*/ function ($routeProvider, $httpProvider, $provide) {
    // needed for translated routes
    $provide.factory('$routeProvider', function () {
        return $routeProvider;
    });
    $provide.factory('$httpProvider', function () {
        return $httpProvider;
    });

    $httpProvider.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

    $httpProvider.interceptors.push(/*@ngInject*/ function($templateCache, Cache) {
        return {
            // check if requested template is already stores in localstorage
            request (request) {
                var url = request.url;
                var cached;
                if (url.includes('.html')) {
                    if ((cached = $templateCache.get(url)) && !Cache.get(url)) {
                        console.debug('Request: Put template in local cache: ' + url);
                        Cache.put(url, cached, {maxAge: 86400});
                    }
                    request.cache = Cache;
                }

                return request;
            }
        };
    });
});
