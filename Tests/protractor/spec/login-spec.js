var helper = require('../helper');

describe('login into raspberry app', function() {

    it('click login in menu', function () {
        browser.get('http://localhost:8080');

        var link = $('a[href="/#login"]');
        expect(link.isPresent()).toBe(true);

        link.click();

        expect($('.form-signin').isPresent()).toBe(true);
    });

    it('try wrong username', function () {
        var username = element(by.model('username'));
        var password = element(by.model('password'));

        var submit = $('.form-signin button[type="submit"]');

        username.sendKeys("wrong");
        password.sendKeys("also wrong");
        submit.click();

        helper.sleep(200);

        helper.expectFlash('Username "wrong" does not exist.');
    });

    it('try wrong password', function () {
        var username = element(by.model('username'));

        var submit = $('.form-signin button[type="submit"]');

        username.clear();
        username.sendKeys("testuser");
        submit.click();

        helper.sleep(200);

        helper.expectFlash('Invalid Password');
    });

    it('try correct credentials', function () {
        var username = element(by.model('username'));
        var password = element(by.model('password'));

        var submit = $('.form-signin button[type="submit"]');

        username.clear();
        password.clear();
        username.sendKeys("testuser");
        password.sendKeys("testpassword");
        submit.click();

        helper.sleep(200);

        helper.expectFlash('Welcome testuser');
    });

    it('check layout after login', function () {
        // todo check menu
        // check dashboard

        var userName = element(by.binding('current_user.username'));
        expect(userName.getText()).toBe('testuser');
        //helper.sleep(20000);

    });
});
