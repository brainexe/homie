// start selenium server:
// webdriver-manager start
// create test user:
// php console user:create testuser testpassword admin

process.envHOMIE_HOST = process.envHOMIE_HOST || 'http://homie';

exports.config = {
    multiCapabilities: [
        {
            'browserName': 'firefox'
        },
        {
            'browserName': 'chrome',
            'chromeOptions': {'args': ['incognito']}
        }
    ],

    specs: [
        'spec/register-spec.js', // todo check for config.registrationEnabled
        'spec/login-spec.js',
        'spec/dashboard-spec.js',
        'spec/switch-spec.js',
        'spec/sensor-spec.js',
        'spec/actions-spec.js',
        'spec/gpio-spec.js',
        'spec/displays-spec.js',
        'spec/speak-spec.js',
        'spec/eggtimer-spec.js',
        'spec/todo-spec.js',
        'spec/shoppinglist-spec.js',
        'spec/webcam-spec.js',
        'spec/status-spec.js',

        'spec/nodes-spec.js',
        'spec/language-spec.js',
        'spec/logout-spec.js'
    ],
    framework: 'jasmine',

    params: {
        login: {
            user: 'testuser',
            password: 'testpassword'
        }
    },

    jasmineNodeOpts: {
        isVerbose: true
    }
};
