
var config  = require('./lib/config');
var arduino = require('duino');

var pin = config['motion.it.pin'];

var board = new arduino.Board({
    debug: config['debug'],
    device: 'ttyUSB0'
});

var button = new arduino.Button({
    board: board,
    pin: pin
});

button.on('up', function(){
    // delete the database!
    console.log('up');
});

button.on('down', function(){
    // delete the database!
    console.log('down');
});

console.log('started motion listener on pin ' + pin);
