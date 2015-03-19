
var controllers = [
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
];

App.ng.value('controllers', controllers);
