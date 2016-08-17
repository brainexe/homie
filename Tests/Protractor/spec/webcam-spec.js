
var helper = require("../helper");

describe('Test "Webcam" component', function() {
    var shotButton = $('.content a[ng-click="takeShot()"]');

    it('Click "Webcam" link in menu', function () {
        var link = helper.getMenuLink("camera");

        link.click();

        expect(browser.getTitle()).toEqual("Webcam");
    });

    it('Click "Take shot"', function () {
        expect(shotButton.isPresent()).toBe(true);
        shotButton.click();
        helper.expectFlash('Cheese');
    });
});
