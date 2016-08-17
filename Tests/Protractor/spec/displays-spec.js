
var helper = require("../helper");

describe('Test "Displays" component', function() {
    it('Click "Webcam" link in menu', function () {
        var link = helper.getMenuLink("displays");

        link.click();
    });
});
