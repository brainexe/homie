
var helper = require('../helper');

describe('Test "Sensor" component', function() {
    var container = $('.chart_container');

    it('Click "Sensor" link in menu', function () {
        var link = helper.getMenuLink("sensor");

        link.click();

        expect(container.isPresent()).toBe(true);
        expect(browser.getTitle()).toEqual("Sensors");
    });
});
