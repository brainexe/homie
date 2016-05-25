
var redis   = require('./lib/redis'),
    config  = require('./lib/config'),
    debug   = require('debug')('arduino'),
    arduino = require('duino');

var client = redis.getClient('arduino-subscribe');
var servos = {};

function registerGpioListener(pin) {
    var button = new arduino.Button({
        board: board,
        pin: pin
    });

    button.on('up', function(){
        console.log('up');
    });

    button.on('down', function(){
        console.log('down');
    });
}

function handleServo(board, pin) {
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
}

function handleDigital(board, pin, value) {
    if (value == '1') {
        board.digitalWrite(pin, board.HIGH);
    } else {
        board.digitalWrite(pin, board.LOW);
    }
}

client.on('message', function (channel, command) {
    var parts = command.split(':');

    var action = parts[0];
    var pin    = parts[1];
    var value  = parts[2];

    debug(action, pin, value);

    switch (action) {
        case 'd':
            handleDigital(board, pin, value);
            break;
        case 'a':
            board.analogWrite(pin, value);
            break;
        case 's':
            handleServo(board, pin);
            break;
        case 'lcd':
            var lcd = new arduino.LCD({
                board: board,
                pins: {rs:12, rw:11, e:10, data:[5, 4, 3, 2]} // todo make LCD-Display configurable
            });
            lcd.begin(1, 1);
            lcd.print(value);
    }
});
client.subscribe("arduino");
