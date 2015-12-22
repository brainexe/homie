var config  = require('./lib/config');
var http    = require("http");
var devices = require('node-metawear/src/device');
var port    = config['metawear.port'];

var currentDevice = null;
devices.discover(function(device) {
    console.log('Discovered device');
    device.on('disconnect', function () {
        console.log('we got disconnected! :( ');
    });

    device.onSetup(function (error) {
        console.log('we are ready');
        currentDevice = device;
    });
});
console.log('start server on port ' + port + '...');

http.createServer(function(request, response){
    if (!currentDevice) {
        response.writeHeader(503, {"Content-Type": "text/plain"});
        response.write("metawear not ready");
        response.end();
        console.error('Device not ready..');
        return;
    }
    switch (request.url) {
        case '/':
        case '/info/':
            response.writeHeader(200, {"Content-Type": "text/plain"});
            response.write("OK");
            response.end();
            break;
        case '/temperature/':
            var temperature = new currentDevice.Temperature(
                currentDevice,
                currentDevice.Temperature.ON_BOARD_THERMISTOR
            );

            temperature.getValue(function(value) {
                response.writeHeader(200, {"Content-Type": "text/plain"});
                response.write("" + value);
                response.end();
            });
            break;
        default:
            response.writeHeader(404, {"Content-Type": "text/plain"});
            response.write("Route not found");
            response.end();
    }
    console.log('HTTP request - ' + request.url);
}).listen(port);
