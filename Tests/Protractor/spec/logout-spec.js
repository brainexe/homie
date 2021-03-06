
var helper = require('../helper');

describe('Logout of homie app', function() {
    it('Click on username in upper-right corner', function () {
        // open user menu (upper right corner)
        var userLink = $('.user-menu');
        expect(userLink.isPresent()).toBe(true);

        userLink.click();

        helper.sleep(200);
    });

    it('Click on logout', function () {
        // click logout
        var userLink = helper.getMenuLink("logout");

        helper.sleep(100);

        userLink.click();

        helper.sleep(500);

        // login link should be visible
        helper.getMenuLink("login");
    });
});
