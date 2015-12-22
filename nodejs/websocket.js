
var config = require('./lib/config'),
    websocket = require('./node_modules/websocket-node/server');

// todo bind to correct port from config
websocket.start(8081);
