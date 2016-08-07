
App.service('Widget.egg_timer', /*@ngInject*/ function(EggTimer, MessageQueue, _, lodash) {
    function updateJobs($scope, jobs) {
        if (!jobs) {
            return;
        }
        $scope.job = lodash.head(jobs);
    }

    return {
        template: '/templates/widgets/egg_timer.html',
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
                EggTimer.setTimer(time).success(function() {
                    MessageQueue.getJobs(EggTimer.JOB_ID, true).success(function(data) {
                        updateJobs($scope, data);
                    });
                });
            };

            $scope.prompt = function() {
                var time = prompt(_('Set Time'));
                EggTimer.setTimer(time);
            };

            $scope.stop = function(job) {
                MessageQueue.deleteJob(job.jobId).then(function() {
                    MessageQueue.getJobs(EggTimer.JOB_ID, true).success(function(data) {
                        updateJobs($scope, data);
                    });
                });
            }
        }
    };
});
