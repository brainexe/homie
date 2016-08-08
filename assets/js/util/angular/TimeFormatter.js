
App.service('TimeFormatter', /*@ngInject*/ function (_) {
    var allowFuture = true,
        strings = {
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
        var seconds = Math.abs(distanceMillis) / 1000;
        var minutes = seconds / 60;
        var hours   = minutes / 60;
        var days    = hours / 24;
        var years   = days / 365;

        var formatter = strings.formatterAgo;
        if (allowFuture && distanceMillis < 0) {
            formatter = strings.formatterFromNow;
        }

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
});
