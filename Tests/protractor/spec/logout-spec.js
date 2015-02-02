var helper = require('../helper');

describe('logout of raspberry app', function() {
    it('click logout', function () {
        //helper.login();

        // open user menu
        var userLink = $('.user-menu');
        userLink.click();

        helper.sleep(600);

        // click logout
        var userLink = $('a[href="#/logout"]');
        userLink.click();

        helper.sleep(600);

        // login link should be visible
        var link = $('a[href="/#login"]');
        expect(link.isPresent()).toBe(true);
    });

});
