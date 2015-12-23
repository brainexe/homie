var helper = require('../helper');

describe('Test "Switch" component', function() {
    var editMode = $('.switch-edit-button');

    it('Click "Switch" link in menu', function () {
        var link = $('a[href="/#switch"]');
        expect(link.isPresent()).toBe(true);

        link.click();
    });
ueuser
    it('Check "edit mode" button is present', function () {
        expect(editMode.isPresent()).toBe(true);
    });

    it('Chick "edit mode" button', function () {
        editMode.click();
         // TODO add more tests
    });
});
