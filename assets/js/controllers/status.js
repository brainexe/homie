
App.controller('StatusController', ['$scope', 'Status', 'Cache', function ($scope, Status, Cache) {
    var REFRESH_INTERVAL = 20000;

    $scope.jobs    = {};
    $scope.cache   = Cache;
    $scope.cacheSize     = JSON.stringify(Cache.info().storageImpl).length / 1000;
    $scope.redisSections = {};

    $scope.$on('message_queue.handled', function(event, data) {
        var job = data.job;
        if ($scope.jobs[job.jobId]) {
            $scope.jobs[job.jobId] = job;
        }
    });

    $scope.update = function () {
        Status.getData().success(function (data) {
            $scope.jobs  = data.jobs;
            $scope.redis = data.redis;
        });
    };

    $scope.update();

    setInterval(function () {
        $scope.update();
    }, REFRESH_INTERVAL);

    /**
     * @param {String} eventId
     */
    $scope.deleteEvent = function (eventId) {
       Status.deleteEvent(eventId).success(function () {
            delete $scope.jobs[eventId];
        });
    };
}]);
