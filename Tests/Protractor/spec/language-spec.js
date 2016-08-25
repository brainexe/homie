
var helper = require('../helper');

describe('Logout of homie app', function() {
    var userLink = $('.user-menu');

    it('Click on username in upper-right corner', function () {
        // open user menu (upper right corner)
        expect(userLink.isPresent()).toBe(true);

        userLink.click();
    });

    it('Change language internally', function () {
        helper.evaluate('changeLanguage("de_DE")');
        helper.sleep(500);
        helper.evaluate('changeLanguage("en_US")');
    });

    it('Close user profile dropdown', function () {
        userLink.click();
    });
});
