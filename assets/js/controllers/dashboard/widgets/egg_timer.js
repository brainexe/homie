
App.service('Widget.egg_timer', ['EggTimer', '_', function(EggTimer, _) {
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
                '10m',
                '20m',
                '30m'
            ];

            EggTimer.getJobs().success(function(data) {
                updateJobs($scope, data.jobs);
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
                if (!confirm(_('Abort job?'))) {
                    return;
                }

                var jobId = job.eventId.split(':')[1];
                EggTimer.deleteTimer(jobId).success(function(jobs) {
                    updateJobs($scope, jobs);
                });
            }
        }
    };
}]);
