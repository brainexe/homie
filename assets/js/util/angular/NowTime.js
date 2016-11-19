
App.factory('nowTime', /* @ngInject */ function ($interval) {
    var nowTime = Date.now();
    $interval(function () {
        nowTime = Date.now();
    }, 1000);

    return () => nowTime;
});
