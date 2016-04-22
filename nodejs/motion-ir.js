
var config  = require('./lib/config');
var arduino = require('duino');

var pin = config['motion.ir.pin'];

var board = new arduino.Board({
    debug: config['debug'],
    device: 'ttyUSB0'
});

board.on('ready', function() {
    console.log('Arduino board is ready! Started motion listener on pin ' + pin);

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
});

console.log('Started motion_ir.js. Waiting for arduino device...');
