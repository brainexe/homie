
var config    = require('./lib/config'),
    common    = require('./lib/common'),
    websocket = require('websocket-node/lib/server');

common.changeUser();

console.log('Start websocket server');

websocket.start(config['socket.internal.port']);
