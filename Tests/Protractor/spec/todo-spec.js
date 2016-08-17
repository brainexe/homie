
var helper = require('../helper');

describe('Test "Todo" component', function() {
    var taskList = $('.content .task-list');

    it('Click "Todo" link in menu', function () {
        var link = helper.getMenuLink("todo");

        link.click();
        helper.sleep(20);

        expect(browser.getTitle()).toEqual("ToDo List");
        expect(taskList.isPresent()).toBe(true);
    });
});
