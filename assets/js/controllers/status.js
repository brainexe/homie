
App.controller('StatusController', ['$scope', 'Status', function ($scope, Status) {
    var REFRESH_INTERVAL = 15000;

    $scope.stats   = {};
    $scope.jobs    = {};
    $scope.redisSections = {};

    $scope.update = function () {
        Status.getData().success(function (data) {
            $scope.stats = data.stats;
            $scope.jobs  = data.jobs;
            $scope.redis = data.redis;
        });
    };

    $scope.update();

    setInterval(function () {
        $scope.update();
    }, REFRESH_INTERVAL);

    $scope.resetStats = function (key) {
       Status.reset(key).success(function () {
            delete $scope.stats[key];
        });
    };

    /**
     * @param {String} eventId
     */
    $scope.deleteEvent = function (eventId) {
       Status.deleteEvent(eventId).success(function () {
            delete $scope.jobs[eventId];
        });
    };
}]);
