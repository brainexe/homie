
var helper = require('../helper');

describe('Test "Actions" component', function() {
    var search = element(by.model('search'));

    it('Click "Actions" link in menu', function () {
        var link = helper.getMenuLink("expression");

        link.click();

        expect(browser.getTitle()).toEqual("Actions");
        expect(search.isPresent()).toBe(true);
    });
});
