
App.Layout = {
    debug:  null,
    $scope: null,

    controllers: [
        // menu
        {controller:'DashboardController', name: 'Dashboard', url: 'dashboard', icon:'th-large', templateUrl: asset('/templates/dashboard.html')},
        {controller:'RadioController', name: 'Home Control', url: 'radio', icon:'home', templateUrl: asset('/templates/radio.html')},
        {controller:'SensorController', name: 'Sensors', url: 'sensor', icon:'stats', templateUrl: asset('/templates/sensor.html')},
        {controller:'GpioController', name: 'GPIO', url: 'gpio', icon:'flash', templateUrl: asset('/templates/gpio.html')},
        {controller:'EspeakController', name: 'Speak', url: 'speak', icon:'bullhorn', templateUrl: asset('/templates/espeak.html')},
        {controller:'FlowerController', name: 'Flowers', url: 'flower', icon:'leaf', templateUrl: asset('/templates/flower.html')},
        {controller:'BlogController', name: 'Blog', url: 'blog', icon:'paperclip', templateUrl: asset('/templates/blog.html')},
        {controller:'EggTimerController', name: 'Egg Timer', url: 'egg_timer', icon:'time', templateUrl: asset('/templates/egg_timer.html')},
        {controller:'TodoController', name: 'ToDo List', url: 'todo', icon:'list', templateUrl: asset('/templates/todo.html')},
        {controller:'ShoppingListController', name: 'Shopping List', url: 'shopping', icon:'list', templateUrl: asset('/templates/shopping_list.html')},
        {controller:'WebcamController', name: 'Webcam', url: 'webcam', icon:'bullhorn', templateUrl: asset('/templates/webcam.html')},
        {controller:'UserController', name: 'User Settings', url: 'user', icon:'user', templateUrl: asset('/templates/user/user.html')},
        {controller:'StatusController', name: 'Status', url: 'status', icon:'stats', templateUrl: asset('/templates/status.html')},

        // admin
        {controller:'AdminUsersController', name: 'Users', url: 'admin/users', icon:'stats', templateUrl: asset('/templates/admin/users.html'), role: 'admin'},

        // private
        {controller:'LoginController', name: 'Login', url: 'login', icon: 'user', is_public: true, templateUrl: asset('/templates/user/login.html')},
        {controller:'RegisterController', name: 'Register', url: 'register', icon: 'user', is_public: true, templateUrl: asset('/templates/user/register.html')},

        // hidden controllers
        {url: 'logout', templateUrl: "/templates/mood.html", controller: "LogoutController"},
        {url: 'user/change_password', templateUrl: "/templates/user/change_password.html", controller: "ChangePasswordController"},
        {url: 'user/otp', templateUrl: "/templates/user/otp.html", controller: "OtpController"},
        {url: 'index', templateUrl: "/templates/index.html", controller: "IndexController"}
    ],

    init: function (debug, current_user) {
        App.Layout.debug = debug;

        App.ng.controller('LayoutController', ['$scope', function ($scope) {
            App.Layout.$scope = $scope;

            $scope.flash_bag = [];
            $scope.current_user = current_user;

            /**
             * @returns {Boolean}
             */
            $scope.isLoggedIn = function () {
                return $scope.current_user && $scope.current_user.id > 0;
            };

            $scope.removeFlash = function(index) {
                $scope.flash_bag.splice(index, 1);
            };

            /**
             * @param {String} type
             * @param {String} message
             */
            $scope.addFlash = function (message, type) {
                type = type || 'success';

                var item = {
                    type: type,
                    message: message
                };

                $scope.flash_bag.push(item);

                window.setTimeout(function () {
                    var index = $scope.flash_bag.indexOf(item);

                    if (index > -1) {
                        $scope.flash_bag.splice(index, 1);
                        $scope.$apply();
                    }
                }, 5000);

                $scope.$apply();
            };

            $scope.search = function(query) {
                $
                    .get('/search/', {query: query})
                    .then(function (data) {
                        console.log(data);
                    });
            };

            $scope.$on('sensor.value', function (event_name, event) {
                var text = '{0}: {1}'.format(event.sensorVo.name, event.valueFormatted);
                App.Notification.show(text);
            });

            $scope.$on('$routeChangeSuccess', function (event, current) {
                if (current.$$route && current.$$route.name) {
                    App.Layout.changeTitle(current.$$route.name);
                }
            });
        }]);

        App.ng.config(['$routeProvider', '$locationProvider', function ($routeProvider, $locationProvider) {
            for (var i in App.Layout.controllers) {
                var metadata = App.Layout.controllers[i];
                $routeProvider.when('/' + metadata.url, metadata);
            }

            $routeProvider.otherwise({redirectTo: '/index'});
        }]);
    },

    /**
     * @param {String} title
     */
    changeTitle: function (title) {
        document.title = title;
    }
};
