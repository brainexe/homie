var config  = require('./lib/config');
var http    = require('http');
var devices = require('node-metawear/src/device');

var port = config['metawear.port'];

var currentDevice = null;
devices.discover(function(device) {
    console.log('discovered device', device.address);
    device.on('disconnect', function () {
        console.log('we got disconnected! :( ');
    });

    device.connectAndSetup(function (error) {
        console.log('we are ready');
        currentDevice = device;
    });
});
console.log('start server on port ' + port + '...');

function writeResponse(response, code, body) {
    if (!response.finished) {
        response.writeHeader(code, {"Content-Type": "text/plain"});
        response.write(body);
        response.end();
    }
}
function handleTemperature(response) {
    var temperature = new currentDevice.Temperature(
        currentDevice,
        currentDevice.Temperature.ON_BOARD_THERMISTOR
    );

    temperature.getValue(function (value) {
        writeResponse(response, 200, "" + value);
    });
}

function handleBrightness(response) {
    var light = new currentDevice.AmbiantLight(currentDevice);

    light.enable(function (value) {
        writeResponse(response, 200, "" + value);
        light.disable();
    });
}

function handlePressure(response) {
    var barometer = new currentDevice.Barometer(currentDevice);

    barometer.enablePressure(function (value) {
        writeResponse(response, 200, "" + value);
        barometer.disable();
    });
}

function handleRequest(request, response) {
    switch (request.url) {
        case '/':
        case '/info/':
            writeResponse(response, 200, 'OK');
            break;
        case '/temperature/':
            handleTemperature(response);
            break;
        case '/pressure/':
            handlePressure(response);
            break;
        case '/brightness/':
            handleBrightness(response);
            break;
        default:
            writeResponse(404, "Route not found");
    }
}

http.createServer(function(request, response) {
    setTimeout(function () {
        writeResponse(response, 503, 'Timeout');
    }, 5000);

    console.log('HTTP request - ' + request.url);

    if (!currentDevice) {
        writeResponse(response, 503, "metawear not ready yet");
        console.error('Device not ready..');
        return;
    }

    handleRequest(request, response);
}).listen(port);
