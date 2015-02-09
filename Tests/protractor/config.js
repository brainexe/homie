// start selenium server:
// webdriver-manager start
// create test user:
// php console user:create testuser testpassword

exports.config = {
    seleniumAddress: 'http://localhost:4444/wd/hub',
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
