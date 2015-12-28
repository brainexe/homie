
App.directive('timeAgo', ['$filter', 'timeAgo', 'nowTime', function ($filter, timeAgo, nowTime) {
    return {
        restrict: 'EA',
        link: function (scope, elem, attrs) {
            var string, fromTime, tooltip;
            var element = angular.element(elem);
            var dateFilter = $filter('date');

            scope.$watch(function () {
                return nowTime() - timeAgo.parse(scope.fromTime);
            }, function (diffSeconds) {
                fromTime = timeAgo.parse(scope.fromTime);

                if (!fromTime) {
                    element.text('--');
                    return;
                }

                string = timeAgo.inWords(diffSeconds);

                if (scope.overdue && nowTime() > fromTime) {
                    elem[0].style.color = '#c00';
                    elem[0].style.fontWeight = 'bold';
                } else if (elem[0].style.color == '#c00'){
                    elem[0].style.color = '';
                    elem[0].style.fontWeight = 'normal';
                }

                tooltip = dateFilter(fromTime, 'medium');
                element.attr('title', tooltip);
                element.attr('tooltip', tooltip);
                // todo make a nice ui-tooltip

                element.text(string);
            });
        },
        scope: {
            fromTime: "=",
            short:    "=",
            overdue:  "="
        }
    };
}]).factory('nowTime', ['$timeout', function ($timeout) {
    var nowTime = Date.now();
    var updateTime = function () {
        $timeout(function () {
            nowTime = Date.now();
            updateTime();
        }, 1000);
    };
    updateTime();
    return function () {
        return nowTime;
    };
}]).factory('timeAgo', [function () {
    var service = {};

    service.settings = {
        allowFuture: true,
        strings: {
            prefixAgo: null,
            prefixFromNow: null,
            suffixAgo: 'ago',
            suffixFromNow: 'from now',
            fewSeconds: 'a few seconds',
            seconds: '%d seconds',
            minute: 'about a minute',
            minutes: '%d minutes',
            hour: 'about an hour',
            hours: 'about %d hours',
            day: 'a day',
            days: '%d days',
            month: 'about a month',
            months: '%d months',
            year: 'about a year',
            years: '%d years',
            numbers: []
        }
    };

    service.inWords = function (distanceMillis) {
        var $l = service.settings.strings;
        var prefix = $l.prefixAgo;
        var suffix = $l.suffixAgo;
        if (service.settings.allowFuture) {
            if (distanceMillis < 0) {
                prefix = $l.prefixFromNow;
                suffix = $l.suffixFromNow;
            }
        }

        var seconds = Math.abs(distanceMillis) / 1000;
        var minutes = seconds / 60;
        var hours   = minutes / 60;
        var days    = hours / 24;
        var years   = days / 365;

        function substitute(stringOrFunction, number) {
            var string = angular.isFunction(stringOrFunction) ?
                stringOrFunction(number, distanceMillis) : stringOrFunction;
            var value = ($l.numbers && $l.numbers[number]) || number;
            return string.replace(/%d/i, value);
        }

        var words =
            seconds < 5 && substitute($l.fewSeconds, Math.round(seconds)) ||
            seconds <= 60 && substitute($l.seconds, Math.round(seconds)) ||
            seconds < 90 && substitute($l.minute, 1) ||
            minutes < 45 && substitute($l.minutes, Math.round(minutes)) ||
            minutes < 90 && substitute($l.hour, 1) ||
            hours < 24 && substitute($l.hours, Math.round(hours)) ||
            hours < 42 && substitute($l.day, 1) ||
            days < 30 && substitute($l.days, Math.round(days)) ||
            days < 45 && substitute($l.month, 1) ||
            days < 365 && substitute($l.months, Math.round(days / 30)) ||
            years < 1.5 && substitute($l.year, 1) ||
            substitute($l.years, Math.round(years));

        var separator = $l.wordSeparator === undefined ? ' ' : $l.wordSeparator;
        return [prefix, words, suffix].join(separator).trim();
    };

    service.parse = function (timesamp) {
        return ~~timesamp * 1000;
    };

    return service;
}]);
