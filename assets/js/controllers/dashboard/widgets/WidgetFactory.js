
App.service('WidgetFactory', [
    'Widget.time',
    'Widget.speak',
    'Widget.egg_timer',
    'Widget.radio',
    'Widget.sensor',
    'Widget.sensor_graph',
    'Widget.status',
    function(TimeWidget, SpeakWidget, EggTimerWidget, RadioWidget, SensorWidget, SensorGraphWidget, StatusWidget) {
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
                case 'status':
                    return StatusWidget;
            }
        };
    }
]);
