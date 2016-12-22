
App.controller('StatusController', /*@ngInject*/ function ($scope, $interval, Status, Cache, MessageQueue, OrderByMixin) {
    angular.extend($scope, OrderByMixin);

    const REFRESH_INTERVAL = 10000;

    $scope.jobs      = {};
    $scope.redisSections = {};
    $scope.cacheSize = 0;
    $scope.cacheKeys = 0;
    $scope.orderBy   = 'timestamp';

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
        $scope.cacheKeys = Cache.info().storageImpl.length;
        $scope.cacheSize = JSON.stringify(Cache.info().storageImpl).length / 1000;
        Status.getData().then(function (data) {
            $scope.jobs  = data.data.jobs;
            $scope.redis = data.data.redis;
        });
    };

    $scope.update();

    /**
     * @param {String} eventId
     */
    $scope.deleteEvent = function (eventId) {
       Status.deleteEvent(eventId).then(function () {
            delete $scope.jobs[eventId];
        });
    };
});
