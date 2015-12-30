
var config    = require('./lib/config'),
    websocket = require('./node_modules/websocket-node/server');

websocket.start(config['socket.internal.port']);
