
App.run(
    ['$routeProvider', 'controllers',
        ($routeProvider, controllers) => {
            // init routing
            controllers = controllers();
            for (var controller of controllers) {
                $routeProvider.when('/' + controller.url, controller);
            }
            $routeProvider.otherwise({redirectTo: '/index'});
        }
    ]
);
