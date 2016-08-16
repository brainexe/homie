var helper = require('../helper');

describe('Test "Status" component', function() {

    it('Click "Status" link in menu', function () {
        var link = helper.getMenuLink("status");
        expect(link.isPresent()).toBe(true);

        link.click();

        expect(browser.getTitle()).toEqual("Status");
    });

    it("Browser cache should be filled with keys", function () {
        $('.content').evaluate('cacheKeys').then(function (count) {
            expect(count > 5).toBe(true);
        });
    });

    it('Delete all test Jobs', function () {
        helper.evaluate('jobs').then(function (jobs) {
           for (var jobId in jobs) {
               var job = jobs[jobId];
               if (job.event.eventName === 'espeak.speak' && job.event.espeak.text === 'test') {
                   $('.content').evaluate(`deleteEvent('${jobId}')`).then(function () {
                       expect(true).toBe(true);
                   });
               }
           }
        });
    });
});
