
App.run(
    ['$routeProvider', 'controllers',
        function ($routeProvider, controllers) {
            // init routing
            controllers = controllers();
            for (var i in controllers) {
                var metadata = controllers[i];
                $routeProvider.when('/' + metadata.url, metadata);
            }
            $routeProvider.otherwise({redirectTo: '/index'});
        }
    ]
);
