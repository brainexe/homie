
App.service("controllers", ["_", function(_) {
    var controller  = "controller";
    var name        = "name";
    var url         = "url";
    var icon        = "icon";
    var templateUrl = "templateUrl";
    var collapsible = "collapsible";

    return () =>
        [
            // menu
            {[controller]:'DashboardController',    [name]: _('Dashboard'),     [url]: 'dashboard',     [icon]:'th-large',  [templateUrl]: '/templates/dashboard/index.html'},
            {[controller]:'SwitchController',       [name]: _('Switches'),      [url]: 'switch',        [icon]:'home',      [templateUrl]: '/templates/switch/switch.html',             [collapsible]:true},
            {[controller]:'SensorController',       [name]: _('Sensors'),       [url]: 'sensor',        [icon]:'stats',     [templateUrl]: '/templates/sensor/sensor.html',             [collapsible]:true},
            {[controller]:'ExpressionController',   [name]: _('Actions'),       [url]: 'expression',    [icon]:'link',      [templateUrl]: '/templates/expression/expression.html',     [collapsible]:true},
            {[controller]:'GpioController',         [name]: _('GPIO'),          [url]: 'gpio',          [icon]:'flash',     [templateUrl]: '/templates/gpio.html',                      [collapsible]:true},
            {[controller]:'DisplaysController',     [name]: _('Displays'),      [url]: 'displays',      [icon]:'text-background', [templateUrl]: '/templates/displays/displays.html',   [collapsible]:true},
            {[controller]:'EspeakController',       [name]: _('Speak'),         [url]: 'speak',         [icon]:'bullhorn',  [templateUrl]: '/templates/espeak.html',                    [collapsible]:true},
            {[controller]:'EggTimerController',     [name]: _('Egg Timer'),     [url]: 'egg_timer',     [icon]:'time',      [templateUrl]: '/templates/egg_timer.html',                 [collapsible]:true},
            {[controller]:'TodoController',         [name]: _('ToDo List'),     [url]: 'todo',          [icon]:'list',      [templateUrl]: '/templates/todo.html',                      [collapsible]:true},
            {[controller]:'ShoppingListController', [name]: _('Shopping List'), [url]: 'shopping',      [icon]:'list',      [templateUrl]: '/templates/shopping_list.html',             [collapsible]:true},
            {[controller]:'WebcamController',       [name]: _('Webcam'),        [url]: 'camera',        [icon]:'bullhorn',  [templateUrl]: '/templates/webcam.html',                    [collapsible]:true},
            {[controller]:'UserController',         [name]: _('User Settings'), [url]: 'user',          [icon]:'user',      [templateUrl]: '/templates/user/user.html'},
            {[controller]:'StatusController',       [name]: _('Status'),        [url]: 'status',        [icon]:'stats',     [templateUrl]: '/templates/status.html', role: 'admin'},

            // admin
            {[controller]:'AdminUsersController',   [name]: _('Users'),         [url]: 'admin/users',   [icon]:'stats',     [templateUrl]: '/templates/admin/users.html', role: 'admin'},
            {[controller]:'AdminNodesController',   [name]: _('Nodes'),         [url]: 'admin/nodes',   [icon]:'stats',     [templateUrl]: '/templates/admin/nodes.html', role: 'admin'},

            // private
            {[controller]:'LoginController',        [name]: _('Login'),         [url]: 'login',         [icon]: 'user', isPublic: true, [templateUrl]: '/templates/user/login.html'},
            {[controller]:'RegisterController',     [name]: _('Register'),      [url]: 'register',      [icon]: 'user', isPublic: true, [templateUrl]: '/templates/user/register.html', checkConfig:(config) => config.registrationEnabled},

            // hidden controllers
            {[url]: 'logout',                       [controller]: "LogoutController"},
            {[url]: 'user/change_password',         [templateUrl]: "/templates/user/change_password.html",  [controller]: "ChangePasswordController"},
            {[url]: 'user/settings',                [templateUrl]: "/templates/user/settings.html",         [controller]: "UserSettingsController"},
            {[url]: 'user/otp',                     [templateUrl]: "/templates/user/otp.html",              [controller]: "OtpController"},
            {[url]: 'user/tokens',                  [templateUrl]: "/templates/user/tokens.html",           [controller]: "UserTokensController"},
            {[url]: 'index',                        [controller]: "IndexController", template: ''}
        ];
}]);
