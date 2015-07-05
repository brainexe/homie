
App.controller('StatusController', ['$scope', 'Status', function ($scope, Status) {
    var REFRESH_INTERVAL = 15000;

    $scope.stats   = {};
    $scope.jobs    = {};
    $scope.redisSections = {};

    $scope.update = function () {
        Status.getData().success(function (data) {
            $scope.stats = data.stats;
            $scope.statsGroup = {};
            for (var key in data.stats) {
                var parts = key.split(':');
                var key1 = parts.splice(0, 1)[0];
                var key2 = parts.join(':');

                if (!$scope.statsGroup[key1]) {
                    $scope.statsGroup[key1] = {};
                }
                $scope.statsGroup[key1][key2] = data.stats[key];
            }
            console.log( $scope.statsGroup);
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
