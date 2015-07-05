
App.service('WidgetFactory', [
    'Widget.time',
    'Widget.speak',
    'Widget.egg_timer',
    'Widget.switch',
    'Widget.sensor',
    'Widget.sensor_graph',
    'Widget.status',
    'Widget.webcam',
    function(TimeWidget, SpeakWidget, EggTimerWidget, SwitchWidget, SensorWidget, SensorGraphWidget, StatusWidget, WebcamWidget) {
        return function(type) {
            switch (type) {
                case 'time':
                    return TimeWidget;
                case 'speak':
                    return SpeakWidget;
                case 'egg_timer':
                    return EggTimerWidget;
                case 'switch':
                    return SwitchWidget;
                case 'sensor':
                    return SensorWidget;
                case 'sensor_graph':
                    return SensorGraphWidget;
                case 'status':
                    return StatusWidget;
                case 'webcam':
                    return WebcamWidget;
            }
        };
    }
]);
