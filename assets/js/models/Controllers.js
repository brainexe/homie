
App.ng.service('controllers', ['_', function(_) {
    return [
        // menu
        {controller:'DashboardController', name: _('Dashboard'), url: 'dashboard', icon:'th-large', templateUrl: asset('/templates/dashboard.html')},
        {controller:'RadioController', name: _('Switches'), url: 'radio', icon:'home', templateUrl: asset('/templates/radio.html')},
        {controller:'SensorController', name: _('Sensors'), url: 'sensor', icon:'stats', templateUrl: asset('/templates/sensor.html')},
        {controller:'ExpressionController', name: _('Actions'), url: 'expression', icon:'link', templateUrl: asset('/templates/expression.html')},
        {controller:'GpioController', name: _('GPIO'), url: 'gpio', icon:'flash', templateUrl: asset('/templates/gpio.html')},
        {controller:'EspeakController', name: _('Speak'), url: 'speak', icon:'bullhorn', templateUrl: asset('/templates/espeak.html')},
        {controller:'FlowerController', name: _('Flowers'), url: 'flower', icon:'leaf', templateUrl: asset('/templates/flower.html')},
        {controller:'EggTimerController', name: _('Egg Timer'), url: 'egg_timer', icon:'time', templateUrl: asset('/templates/egg_timer.html')},
        {controller:'TodoController', name: _('ToDo List'), url: 'todo', icon:'list', templateUrl: asset('/templates/todo.html')},
        {controller:'ShoppingListController', name: _('Shopping List'), url: 'shopping', icon:'list', templateUrl: asset('/templates/shopping_list.html')},
        {controller:'WebcamController', name: _('Webcam'), url: 'webcam', icon:'bullhorn', templateUrl: asset('/templates/webcam.html')},
        {controller:'UserController', name: _('User Settings'), url: 'user', icon:'user', templateUrl: asset('/templates/user/user.html')},
        {controller:'StatusController', name: _('Status'), url: 'status', icon:'stats', templateUrl: asset('/templates/status.html')},

        // admin
        {controller:'AdminUsersController', name: _('Users'), url: 'admin/users', icon:'stats', templateUrl: asset('/templates/admin/users.html'), role: 'admin'},

        // private
        {controller:'LoginController', name: _('Login'), url: 'login', icon: 'user', isPublic: true, templateUrl: asset('/templates/user/login.html')},
        {controller:'RegisterController', name: _('Register'), url: 'register', icon: 'user', isPublic: true, templateUrl: asset('/templates/user/register.html')},

        // hidden controllers
        {url: 'logout', templateUrl: "/templates/index.html", controller: "LogoutController"},
        {url: 'user/change_password', templateUrl: "/templates/user/change_password.html", controller: "ChangePasswordController"},
        {url: 'user/otp', templateUrl: "/templates/user/otp.html", controller: "OtpController"},
        {url: 'user/tokens', templateUrl: "/templates/user/tokens.html", controller: "UserTokensController"},
        {url: 'index', templateUrl: "/templates/index.html", controller: "IndexController"}
    ]
}]);
