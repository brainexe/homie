
var helper = require('../helper');

describe('Test "Speak" component', function() {
    var text  = element(by.model('text'));
    var delay = element(by.model('delay'));

    var submit = $('.content button[type="submit"]');

    it('Click "Speak" link in menu', function () {
        var link = helper.getMenuLink("speak");

        link.click();

        expect(submit.isPresent()).toBe(true);
        expect(browser.getTitle()).toEqual("Speak");
    });

    it('Speak anything', function () {
        text.sendKeys("test");

        submit.click();
    });

    it('Speak delayed', function () {
        helper.evaluate('jobs|objectSize').then(function(oldCount) {
            text.clear();
            text.sendKeys("test");
            delay.sendKeys("10h");

            submit.click();

            helper.evaluate('jobs|objectSize').then(function(newCount) {
                expect(newCount).toEqual(oldCount + 1);

            });
        });
    });
});
