
// todo use time instead of nowTime?
App.directive('timeAgo', ['$filter', 'timeFormatter', 'nowTime', function ($filter, timeFormatter, nowTime) {
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
            var tooltip = dateFilter(fromTime, 'medium');
            element.attr('title', tooltip);
            element.attr('tooltip', tooltip);

            scope.$watch(nowTime, function (now) {
                if (scope.overdue && now > fromTime) {
                    elem[0].style.color = '#c00';
                    elem[0].style.fontWeight = 'bold';
                } else {
                    elem[0].style.color = '';
                    elem[0].style.fontWeight = 'normal';
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
}]).factory('nowTime', ['$interval', function ($interval) {
    var nowTime = Date.now();
    $interval(function () {
        nowTime = Date.now();
    }, 1000);

    return function () {
        return nowTime;
    };
}]).factory('timeFormatter', ['_', function (_) {
    var settings = {
        allowFuture: true
    };

    var strings = {
        formatterAgo: _('%s ago'),
        formatterFromNow: _('%s from now'),
        fewSeconds: _('a few seconds'),
        seconds: _('%d seconds'),
        minute: _('about a minute'),
        minutes: _('%d minutes'),
        hour: _('about an hour'),
        hours: _('about %d hours'),
        day: _('a day'),
        days: _('%d days'),
        month: _('about a month'),
        months: _('%d months'),
        year: _('about a year'),
        years: _('%d years')
    };

    function inWords(distanceMillis) {
        var formatter = strings.formatterAgo;
        if (settings.allowFuture && distanceMillis < 0) {
            formatter = strings.formatterFromNow;
        }

        var seconds = Math.abs(distanceMillis) / 1000;
        var minutes = seconds / 60;
        var hours   = minutes / 60;
        var days    = hours / 24;
        var years   = days / 365;

        function substitute(stringOrFunction, number) {
            var string = angular.isFunction(stringOrFunction) ?
                stringOrFunction(number, distanceMillis) : stringOrFunction;
            return string.replace(/%d/i, number);
        }

        var words =
            seconds < 3 && substitute(strings.fewSeconds, Math.round(seconds)) ||
            seconds <= 60 && substitute(strings.seconds, Math.round(seconds)) ||
            seconds < 90 && substitute(strings.minute, 1) ||
            minutes < 45 && substitute(strings.minutes, Math.round(minutes)) ||
            minutes < 90 && substitute(strings.hour, 1) ||
            hours < 24 && substitute(strings.hours, Math.round(hours)) ||
            hours < 42 && substitute(strings.day, 1) ||
            days < 30 && substitute(strings.days, Math.round(days)) ||
            days < 45 && substitute(strings.month, 1) ||
            days < 365 && substitute(strings.months, Math.round(days / 30)) ||
            years < 1.5 && substitute(strings.year, 1) ||
            substitute(strings.years, Math.round(years));

        return formatter.replace(/%s/, words);
    }

    return inWords;
}]);
