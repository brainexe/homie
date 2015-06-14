
App.service('WidgetFactory', [
    'Widget.time',
    'Widget.speak',
    'Widget.egg_timer',
    'Widget.radio',
    'Widget.sensor',
    'Widget.sensor_graph',
    function(TimeWidget, SpeakWidget, EggTimerWidget, RadioWidget, SensorWidget, SensorGraphWidget) {
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
                case 'sensor_graph':
                    return SensorGraphWidget;
            }
        };
    }
]);
