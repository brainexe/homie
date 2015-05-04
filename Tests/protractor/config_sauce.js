// start selenium server:
// webdriver-manager start
// create test user:
// php console user:create testuser testpassword

process.env.RASPBERRY_HOST = process.env.RASPBERRY_HOST || 'https://raspberry.mdoetsch.de';

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