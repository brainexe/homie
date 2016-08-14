var fs = require('fs');

var self = module.exports;

module.exports.closeAllFlashs = function() {
    $$('.alert button').click();
};

module.exports.sleep = function(delay) {
    browser.driver.sleep(delay);
};

module.exports.expectFlash = function(expectedText) {
    browser.driver.sleep(100);

    $('.content-header').getInnerHtml().then(function (html) {
        console.log(expectedText, html);
        // todo
        expect(html.indexOf(expectedText) != -1).toBe(true);
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

module.exports.restart = function() {
    browser = browser.forkNewDriverInstance();
    browser.quit();
};
