
var pm2 = require('pm2');
var config = require('./lib/config');

pm2.connect(function() {
    config['pm2.apps'].forEach(function(entry) {
        pm2.stop(entry.name);
    });
    pm2.stop('all');
});
