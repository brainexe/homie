var fs = require('fs');

var self = module.exports;

module.exports.closeAllFlashs = function() {
    $$('.alert button').click();
};

module.exports.sleep = function(delay) {
    browser.driver.sleep(delay);
};

module.exports.expectFlash = function(expectedText) {
    $('.content-header').getInnerHtml().then(function (html) {
        expect(html.indexOf(expectedText)).not.toBe(-1);
        self.closeAllFlashs();
    });
};

module.exports.takeScreenshot = function(filename) {
    function writeScreenShot(data, filename) {
        var stream = fs.createWriteStream(filename);

        stream.write(new Buffer(data, 'base64'));
        stream.end();
    }

    browser.takeScreenshot().then(function (png) {
        writeScreenShot(png, filename);
    });
};

module.exports.login = function() {
    var link = $('a[href="/#login"]');

    self.sleep(4000);

    console.log(link);
    if (!link.isPresent()) {
        // already logged in
        return;
    }

    var username = element(by.model('username'));
    var password = element(by.model('password'));

    username.sendKeys("testuser"); // TODO use config
    password.sendKeys("testpassword");

    var submit = $('.form-signin button[type="submit"]');
    submit.click();

    self.sleep(200);

    self.expectFlash('Welcome testuser');
};


module.exports.restart = function() {
    browser = browser.forkNewDriverInstance();
    browser.quit();
};
