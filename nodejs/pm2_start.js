
var pm2     = require('pm2');
var config  = require('./lib/config');
var process = require('process');

var args = process.argv.slice(2);

pm2.connect(function() {
    var apps = config['pm2.apps'].map(function(entry) {
        entry.script     = entry.name + ".js";
        entry.out_file   = "../logs/pm-" + entry.name + ".log";
        entry.error_file = "../logs/pm-" + entry.name + ".log";
        entry.merge_logs = true;
        entry.watch      = false;
        entry.instances  = entry.instances || 1;
        return entry;
    });

    pm2.start(apps, function(err, processes) {
        if (err) {
            console.error(err);
            process.exit(2);
        }
        if (args.indexOf('--no-deamon') === -1) {
            pm2.disconnect();
        } else {
            process.on( "SIGINT", function() {
                pm2.disconnect();
                process.exit();
            });
        }
    });
});
