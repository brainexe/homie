
var MotionConfig  = require('node-motion/config');
var MotionHandler = require('node-motion/motion');
var ViewServer    = require('node-motion/server');
var homieConfig   = require('./lib/config');
var motionConfig  = require('./lib/motion/motion.json');

var port = homieConfig['motion.port'];

motionConfig['videodevice']    = homieConfig['webcam.device'];
motionConfig['on_event_start'] = "curl -X GET http://" + homieConfig['server.host'] + "/motion/add/";

var motion = new MotionHandler();
var config = new MotionConfig({
    params: motionConfig
});
var viewServer = new ViewServer(port);

config.on('info:configWritten', function (filename) {
    motion.setConfig(filename);
    motion.start();
    viewServer.start();
});

motion
    .on('info', function (msg) {
        console.log('[MOTION INFO]', msg);
    })
    .on('debug', function (msg) {
        console.log('[MOTION DEBUG]', msg);
    })
    .on('msg', function (msg) {
        if (msg.action === 'stream' && msg.value !== null) {
            viewServer.streamPort = msg.value;
            console.log('[NODE-MOTION] Preview started on http://localhost:' + port);
        }
    })
    .on('exit', function (msg) {
        console.log('[MOTION EXIT]', msg);
        config.deleteConfig();
    });
