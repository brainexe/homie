
var config    = require('./lib/config'),
    websocket = require('websocket-node/server');

websocket.start(config['socket.internal.port']);
