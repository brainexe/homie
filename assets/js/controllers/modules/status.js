
App.controller('StatusController', /*@ngInject*/ function ($scope, $interval, Status, Cache, MessageQueue) {
    const REFRESH_INTERVAL = 10000;

    $scope.jobs    = {};
    $scope.redisSections = {};
    $scope.cacheSize = 0;
    $scope.cacheKeys = 0;

    var interval = $interval(() => $scope.update(), REFRESH_INTERVAL);

    $scope.$on('$destroy', function() {
        $interval.cancel(interval);
    });

    $scope.$on(MessageQueue.JOBS_HANDLED, function(event, data) {
        var job = data.job;
        if ($scope.jobs[job.jobId]) {
            $scope.jobs[job.jobId] = job;
        }
    });

    $scope.update = function () {
        var key = $scope.cacheKeys = Cache.info().storageImpl.length;
        $scope.cacheSize = JSON.stringify(key) / 1000;
        Status.getData().success(function (data) {
            $scope.jobs  = data.jobs;
            $scope.redis = data.redis;
        });
    };

    $scope.update();

    /**
     * @param {String} eventId
     */
    $scope.deleteEvent = function (eventId) {
       Status.deleteEvent(eventId).success(function () {
            delete $scope.jobs[eventId];
        });
    };
});
