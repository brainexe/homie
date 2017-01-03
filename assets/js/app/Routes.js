
App.run(
    ['$routeProvider', 'controllers',
        ($routeProvider, controllers) => {
            controllers = controllers();
            for (let controller of controllers) {
                $routeProvider.when('/' + controller.url, controller);
            }
            $routeProvider.otherwise({redirectTo: '/index'});
        }
    ]
);
