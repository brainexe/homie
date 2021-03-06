var helper = require('../helper');
var config = require('../../../cache/config.json');

describe('Register at homie app', function() {
    if (!config["application.registration_enabled"]) {
        it('Registertration is disabled: no "register" button should be visible in menu', function () {
            browser.get(process.envHOMIE_HOST);
            var link = $(`a[href="/#register"]`);

            expect(link.isPresent()).toBe(false);
        });

        return;
    }

    var username = element(by.model('username'));
    var password = element(by.model('password'));

    var submit = $('.form-signin button[type="submit"]');

    it('Click "register" in menu', function () {
        browser.get(process.envHOMIE_HOST);

        var link = helper.getMenuLink("register");

        link.click();

        expect($('.form-signin').isPresent()).toBe(true);

        expect(browser.getTitle()).toEqual("Register");
    });

    it('Try empty username', function () {
        expect(submit.isPresent()).toBe(true);

        username.sendKeys("u");
        password.sendKeys("Password");

        submit.click();

        helper.expectFlash('Username must not be empty');
    });

    it('Try empty password', function () {
        expect(submit.isPresent()).toBe(true);

        username.clear();
        password.clear();
        username.sendKeys("username");
        password.sendKeys("p");
        submit.click();

        helper.expectFlash('Password must not be empty');
    });

    it('Try already existing username (testuser)', function () {
        expect(submit.isPresent()).toBe(true);

        username.clear();
        password.clear();
        username.sendKeys("testuser");
        password.sendKeys("testpassword");
        submit.click();

        helper.expectFlash('User testuser already exists');
    });
});
