var helper = require('../helper');

describe('Test "Speak" component', function() {
    var text = element(by.model('text'));
    var delay = element(by.model('delay'));

    var submit = $('.content button[type="submit"]');

    it('Click "Speak" link in menu', function () {
        var link = $('a[href="/#speak"]');
        expect(link.isPresent()).toBe(true);

        link.click();
        browser.ignoreSynchronization = true;

        expect($('.content button[type="submit"]').isPresent()).toBe(true);
    });

    it('Speak anything', function () {
        text.sendKeys("test");

        submit.click();
    });

    it('Speak delayed', function () {
        text.sendKeys("test");
        delay.sendKeys("10h");

        var submit = $('.content button[type="submit"]');
        submit.click();
    });

    // TODO check jobs
});
