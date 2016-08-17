
var helper = require('../helper');

describe('Login into homie app', function() {
    var username = element(by.model('username'));
    var password = element(by.model('password'));

    var submit = $('.form-signin button[type="submit"]');

    it('Click "login" in menu', function () {
        var link = helper.getMenuLink('login');
        link.click();

        expect($('.form-signin').isPresent()).toBe(true);

        expect(browser.getTitle()).toEqual("Login");
    });

    it('Try wrong username', function () {
        expect(submit.isPresent()).toBe(true);

        username.sendKeys("wrong");
        password.sendKeys("also wrong");
        submit.click();

        helper.expectFlash('Username "wrong" does not exist.');
    });

    it('Try wrong password', function () {
        username.clear();
        username.sendKeys("testuser");
        submit.click();

        helper.expectFlash('Invalid Password');
    });

    it('Try correct credentials', function () {
        expect(submit.isPresent()).toBe(true);

        username.clear();
        password.clear();
        username.sendKeys("testuser");
        password.sendKeys("testpassword");
        submit.click();

        helper.expectFlash("Welcome back testuser!");

        helper.getMenuLink("dashboard");
    });
});
