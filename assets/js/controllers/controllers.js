
var controllers = [
    // menu
    {controller:'DashboardController', name: gettext('Dashboard'), url: 'dashboard', icon:'th-large', templateUrl: asset('/templates/dashboard.html')},
    {controller:'RadioController', name: gettext('Switches'), url: 'radio', icon:'home', templateUrl: asset('/templates/radio.html')},
    {controller:'SensorController', name: gettext('Sensors'), url: 'sensor', icon:'stats', templateUrl: asset('/templates/sensor.html')},
    {controller:'ExpressionController', name: gettext('Actions'), url: 'expression', icon:'link', templateUrl: asset('/templates/expression.html')},
    {controller:'GpioController', name: gettext('GPIO'), url: 'gpio', icon:'flash', templateUrl: asset('/templates/gpio.html')},
    {controller:'EspeakController', name: gettext('Speak'), url: 'speak', icon:'bullhorn', templateUrl: asset('/templates/espeak.html')},
    {controller:'FlowerController', name: gettext('Flowers'), url: 'flower', icon:'leaf', templateUrl: asset('/templates/flower.html')},
    {controller:'BlogController', name: gettext('Blog'), url: 'blog', icon:'paperclip', templateUrl: asset('/templates/blog.html')},
    {controller:'EggTimerController', name: gettext('Egg Timer'), url: 'egg_timer', icon:'time', templateUrl: asset('/templates/egg_timer.html')},
    {controller:'TodoController', name: gettext('ToDo List'), url: 'todo', icon:'list', templateUrl: asset('/templates/todo.html')},
    {controller:'ShoppingListController', name: gettext('Shopping List'), url: 'shopping', icon:'list', templateUrl: asset('/templates/shopping_list.html')},
    {controller:'WebcamController', name: gettext('Webcam'), url: 'webcam', icon:'bullhorn', templateUrl: asset('/templates/webcam.html')},
    {controller:'UserController', name: gettext('User Settings'), url: 'user', icon:'user', templateUrl: asset('/templates/user/user.html')},
    {controller:'StatusController', name: gettext('Status'), url: 'status', icon:'stats', templateUrl: asset('/templates/status.html')},

    // admin
    {controller:'AdminUsersController', name: gettext('Users'), url: 'admin/users', icon:'stats', templateUrl: asset('/templates/admin/users.html'), role: 'admin'},

    // private
    {controller:'LoginController', name: gettext('Login'), url: 'login', icon: 'user', is_public: true, templateUrl: asset('/templates/user/login.html')},
    {controller:'RegisterController', name: gettext('Register'), url: 'register', icon: 'user', is_public: true, templateUrl: asset('/templates/user/register.html')},

    // hidden controllers
    {url: 'mood', templateUrl: "/templates/mood.html", controller: "MoodController"},
    {url: 'logout', templateUrl: "/templates/mood.html", controller: "LogoutController"},
    {url: 'user/change_password', templateUrl: "/templates/user/change_password.html", controller: "ChangePasswordController"},
    {url: 'user/otp', templateUrl: "/templates/user/otp.html", controller: "OtpController"},
    {url: 'index', templateUrl: "/templates/index.html", controller: "IndexController"}
];

App.ng.value('controllers', controllers);
