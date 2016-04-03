
var config    = require('./lib/config'),
    websocket = require('websocket-node/lib/server');

websocket.start(config['socket.internal.port']);
