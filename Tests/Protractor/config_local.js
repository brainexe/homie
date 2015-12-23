// start selenium server:
// webdriver-manager start
// create test user:
// php console user:create testuser testpassword admin

process.envHOMIE_HOST = process.envHOMIE_HOST || 'http://homie';

exports.config = {
    sauceUser: process.env.SAUCE_USERNAME,
    sauceKey:  process.env.SAUCE_ACCESS_KEY,

    capabilities: {
        'browserName': 'chrome'
    },

    specs: [
        'spec/register-spec.js',
        'spec/login-spec.js',
        'spec/speak-spec.js',
        'spec/eggtimer-spec.js',
        'spec/status-spec.js',
        'spec/switch-spec.js',
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
