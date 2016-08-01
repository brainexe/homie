
App.controller('StatusController', ['$scope', '$interval', 'Status', 'Cache', function ($scope, $interval, Status, Cache) {
    var REFRESH_INTERVAL = 10000;

    $scope.jobs    = {};
    $scope.cache   = Cache;
    $scope.redisSections = {};
    $scope.cacheSize = 0;

    var interval = $interval(function () {
        $scope.update();
    }, REFRESH_INTERVAL);

    $scope.$on('message_queue.handled', function(event, data) {
        var job = data.job;
        if ($scope.jobs[job.jobId]) {
            $scope.jobs[job.jobId] = job;
        }
    });

    $scope.$on('$destroy', function() {
        $scope.cacheSize = JSON.stringify(Cache.info().storageImpl).length / 1000;
        $interval.cancel(interval);
    });

    $scope.update = function () {
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
}]);
