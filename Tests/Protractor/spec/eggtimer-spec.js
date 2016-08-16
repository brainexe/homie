
var helper = require('../helper');

describe('Test "Egg timer" component', function() {
    var text = element(by.model('text'));
    var time = element(by.model('time'));

    var submit = $('.content button[type="submit"]');

    it('Click "Egg Timer" link in menu', function () {
        var link = helper.getMenuLink("egg_timer");

        browser.ignoreSynchronization = true;
        link.click();

        expect($('.content button[type="submit"]').isPresent()).toBe(true);
    });

    it('Add egg timer with text for now', function () {
        text.sendKeys("Fertig");

        submit.click();
    });
});
