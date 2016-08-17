var helper = require('../helper');

describe('Test "Dashboard" component', function() {
    var content = $('.dashboard');

    it('Click "Dashboard" link in menu', function () {
        var link = helper.getMenuLink("dashboard");

        link.click();

        expect(content.isPresent()).toBe(true);

        var widgets = content.$('.widget');
        expect(widgets.isPresent()).toBe(true);
    });

    it("Click 'Add Widget' button should open modal", function () {
        var link  = $('nav .glyphicon-plus');
        var modal = $('.modal-content');
        var closeButton = $('.modal-content .close');

        expect(link.isPresent()).toBe(true);
        expect(modal.isPresent()).toBe(false);
        expect(closeButton.isPresent()).toBe(false);

        link.click();

        expect(modal.isPresent()).toBe(true);
        expect(closeButton.isPresent()).toBe(true);

        closeButton.click();

        expect(modal.isPresent()).toBe(false);
    });
});
