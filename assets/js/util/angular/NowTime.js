
App.factory('nowTime', /* @ngInject */ function ($rootScope, $interval) {
    var nowTime = Date.now();
    $interval(function () {
        nowTime = Date.now();
        $rootScope.$broadcast('secondTimer', nowTime);
    }, 1000);

    return () => nowTime;
});
