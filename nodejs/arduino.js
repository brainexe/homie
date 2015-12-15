var redis   = require('./lib/redis'),
    config  = require('./lib/config'),
    debug   = require('debug')('arduino'),
    arduino = require('duino'),
    colors  = require('colors');

var client = redis.getClient('arduino-subscribe');
var servos = {};
var board  = new arduino.Board({
    device: "USB0"
});

board.on('error', function(err) {
   console.error(err);
});

client.on('message', function (channel, command) {
    var parts = command.split(':');

    var action = parts[0];
    var pin    = parts[1];
    var value  = parts[2];

    debug(action, pin, value);

    switch (action) {
        case 'd':
            if (value == '1') {
                board.digitalWrite(pin, board.HIGH);
            } else {
                board.digitalWrite(pin, board.LOW);
            }
            break;
        case 'a':
            board.analogWrite(pin, value);
            break;
        case 's':
            var servo;
            if (servos[pin]) {
                servo = servos[pin];
            } else {
                servo = new arduino.Servo({
                    board: board,
                    pin: pin
                });
                servo.attach();
            }

            servo.write(value);
            break;
    }
});
client.subscribe("arduino");
