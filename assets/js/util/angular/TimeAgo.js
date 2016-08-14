
App.directive('timeAgo', /*@ngInject*/ function ($filter, TimeFormatter, nowTime) {
    var dateFilter = $filter('date');

    return {
        restrict: 'EA',
        link ($scope, elem) {
            var element = angular.element(elem);
            var style   = elem[0].style;
            $scope.$watch(nowTime, function (now) {
                var fromTime = ~~$scope.fromTime * 1000;
                if (!fromTime) {
                    element.text('--');
                    return;
                }

                var diffSeconds = now - fromTime;
                var string = TimeFormatter(diffSeconds);
                var tooltip = dateFilter(fromTime, 'medium');

                if ($scope.overdue && now > fromTime) {
                    style.color = '#c00';
                    style.fontWeight = 'bold';
                } else {
                    style.color = '';
                    style.fontWeight = 'normal';
                }

                element.attr('title', tooltip);
                element.attr('tooltip', tooltip);
                element.text(string);
            });
        },
        scope: {
            fromTime: "=",
            short:    "=",
            overdue:  "="
        }
    };
});
