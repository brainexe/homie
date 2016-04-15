var arduino = require('duino');

var board = new arduino.Board({
    // debug: true,
    device: 'ttyUSB0'
});

var button = new arduino.Button({
    board: board,
    pin: 3
});

button.on('up', function(){
    // delete the database!
    console.log('up');
});

button.on('down', function(){
    // delete the database!
    console.log('down');
});
