
App.directive('timeAgo', /*@ngInject*/ function ($filter, TimeFormatter, nowTime) {
    var dateFilter = $filter('date');

    return {
        restrict: 'EA',
        link: function (scope, elem) {
            var element = angular.element(elem);
            var fromTime = ~~scope.fromTime * 1000;
            if (!fromTime) {
                element.text('--');
                return;
            }

            // todo make a nice ui-tooltip
            var style = elem[0].style;
            var tooltip = dateFilter(fromTime, 'medium');
            element.attr('title', tooltip);
            element.attr('tooltip', tooltip);

            scope.$watch(nowTime, function (now) {
                if (scope.overdue && now > fromTime) {
                    style.color = '#c00';
                    style.fontWeight = 'bold';
                } else {
                    style.color = '';
                    style.fontWeight = 'normal';
                }

                var diffSeconds = now - fromTime;
                var string = timeFormatter(diffSeconds);
                element.text(string);
            });
        },
        scope: {
            fromTime: "=",
            short:    "=",
            overdue:  "="
        }
    };
}).factory('nowTime', /*@ngInject*/ function ($interval) {
    var nowTime = Date.now();
    $interval(function () {
        nowTime = Date.now();
    }, 1000);

    return function () {
        return nowTime;
    };
});
