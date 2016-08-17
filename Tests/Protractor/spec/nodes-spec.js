
var helper = require('../helper');

describe('Test "Nodes" component', function() {
    var addButton = $('.content .glyphicon-plus');

    it('Click "Nodes" link in menu', function () {
        var link = helper.getMenuLink("admin/nodes");

        link.click();
        helper.sleep(20);

        expect(browser.getTitle()).toEqual("Nodes");
        expect(addButton.isPresent()).toBe(true);
    });
});
