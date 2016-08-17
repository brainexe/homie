
var helper = require('../helper');

describe('Test "Actions" component', function() {
    var saerch = element(by.model(`search`));

    it('Click "Actions" link in menu', function () {
        var link = helper.getMenuLink("expression");

        link.click();
        helper.sleep(20);

        expect(browser.getTitle()).toEqual("Actions");
        expect(saerch.isPresent()).toBe(true);
    });
});
