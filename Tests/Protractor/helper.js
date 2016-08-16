var fs = require('fs');

var self = module.exports;

module.exports.closeAllFlashs = function() {
    $$('.alert button').click();
};

module.exports.sleep = function(delay) {
    browser.driver.sleep(delay);
};

module.exports.evaluate = function(expression) {
    return $('.content').evaluate(expression);
};

module.exports.getMenuLink = function(url) {
    var link = $(`a[href="/#${url}"]`);

    expect(link.isPresent()).toBe(true);

    return link;
};

module.exports.expectFlash = function(expectedText) {
    browser.sleep(100);
    browser.ignoreSynchronization = true;

    $('.content-header').getInnerHtml().then(function (html) {
        expect(html.includes(expectedText)).toBe(true);
        self.closeAllFlashs();
        browser.ignoreSynchronization = false;
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

module.exports.restart = function() {
    browser = browser.forkNewDriverInstance();
    browser.quit();
};
