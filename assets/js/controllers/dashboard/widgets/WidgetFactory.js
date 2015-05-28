
App.ng.service('WidgetFactory', [
    'Widget.time',
    'Widget.speak',
    'Widget.egg_timer',
    'Widget.radio',
    'Widget.sensor',
    function(TimeWidget, SpeakWidget, EggTimerWidget, RadioWidget, SensorWidget) {
        return function(type) {
            switch (type) {
                case 'time':
                    return TimeWidget;
                case 'speak':
                    return SpeakWidget;
                case 'egg_timer':
                    return EggTimerWidget;
                case 'radio':
                    return RadioWidget;
                case 'sensor':
                    return SensorWidget;
            }
        };
    }
]);
