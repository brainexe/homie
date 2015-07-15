
App.service('Widget.egg_timer', ['EggTimer', 'MessageQueue', '_', function(EggTimer, MessageQueue, _) {
    function updateJobs($scope, jobs) {
        if (!jobs) {
            return;
        }
        $scope.job = jobs[Object.keys(jobs)[0]];
    }

    return {
        render: function($scope) {
            $scope.times = [
                '2m',
                '5m',
                '15m',
                '30m'
            ];

            MessageQueue.getJobs(EggTimer.JOB_ID, true).success(function(data) {
                updateJobs($scope, data);
            });

            $scope.start = function(time) {
                EggTimer.setTimer(time).success(function(jobs) {
                    updateJobs($scope, jobs);
                });
            };

            $scope.prompt = function() {
                var time = prompt(_('Set Time'));
                EggTimer.setTimer(time);
            };

            $scope.stop = function(job) {
                if (!confirm(_('Abort job?'))) { //todo ngConfirm
                    return;
                }

                MessageQueue.deleteJob(job.eventId).then(function() {
                    MessageQueue.getJobs(EggTimer.JOB_ID, true).success(function(data) {
                        updateJobs($scope, data);
                    });
                });
            }
        }
    };
}]);
