
var helper = require('../helper');

describe('Test "GPIO" component', function() {
    var select = $('.gpio-node-selection');

    it('Click "GPIO" link in menu', function () {
        var link = helper.getMenuLink("gpio");

        link.click();

        expect(select.isPresent()).toBe(true);
        expect(browser.getTitle()).toEqual("GPIO");
    });
});
