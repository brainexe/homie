// start selenium server:
// webdriver-manager start
// create test user:
// php console user:create testuser testpassword

var baseConfig = require('./config_local');

Object.assign(exports.config, baseConfig, {
    sauceUser: process.env.SAUCE_USERNAME,
    sauceKey:  process.env.SAUCE_ACCESS_KEY,
});
