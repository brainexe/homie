var helper = require('../helper');

describe('Test "Status" component', function() {

    it('Click "Status" link in menu', function () {
        var link = $('a[href="/#status"]');
        expect(link.isPresent()).toBe(true);

        link.click();
        browser.ignoreSynchronization = true;
    });

    it('Count rows', function () {
        helper.sleep(500);

        element.all(by.repeater('job in jobs')).then(function (rows) {
            expect(rows.length > 0).toBe(true);
            for (var i in rows) {
                var row = rows[i];

                // TODO
                row.getInnerHtml(function(html) {
                   console.log(html);
                });
                //console.log(row.innerHTML);
            }
        });
    });
});
