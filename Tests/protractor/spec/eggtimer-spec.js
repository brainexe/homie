var helper = require('../helper');

describe('Test "Egg timer" component', function() {
    var text = element(by.model('text'));
    var time = element(by.model('time'));

    var submit = $('.content button[type="submit"]');

    it('Click "Egg Timer" link in menu', function () {
        var link = $('a[href="/#egg_timer"]');
        expect(link.isPresent()).toBe(true);

        link.click();
        browser.ignoreSynchronization = true;

        expect($('.content button[type="submit"]').isPresent()).toBe(true);
    });

    it('Add egg timer with text for now', function () {
        text.sendKeys("Fertig");

        submit.click();
    });
});
