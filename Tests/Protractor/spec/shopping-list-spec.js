
var helper = require('../helper');

describe('Test "Shopping list" component', function() {
    var text = element(by.model('itemText'));

    it('Click "Shopping list" link in menu', function () {
        var link = helper.getMenuLink("shopping");

        link.click();

        expect(browser.getTitle()).toEqual("Shopping List");
        expect(text.isPresent()).toBe(true);
    });
});
