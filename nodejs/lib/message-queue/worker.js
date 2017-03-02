var redis = require('../redis'),
    config = require('../config'),
    childProcess = require('child_process'),
    fs = require('fs');

require('colors');

var client          = redis.getClient('mq-worker-delayed');
var clientImmediate = redis.getClient('mq-worker-immediate');

module.exports = {
    run: function() {
        var script = fs.readFileSync(__dirname + '/script.lua');

        client.scriptAsync('load', script).then(function(sha1) {
            function callJob(data) {
                if (!data) {
                    return;
                }
                var payload = data[1];
                if (!payload) {
                    return;
                }

                var process = childProcess.spawn('php', ['console', 'message:exec', '--ansi', '--no-interaction', payload], {
                    cwd:        config['application.root'],
                    timeout:    30000
                });

                process.stdout.on('data', function (data) {
                    console.log('stdout', data.toString());
                });

                process.stderr.on('data', function (data) {
                    console.log('stderr', data.toString());
                });

                process.on('error', function (code) {
                    console.log('error', arguments);
                });
            }

            function handleJob() {
                fetchJob().then(function(payload) {
                    callJob(payload);
                });
            }

            function fetchJob() {
                return client.EVALSHAAsync(sha1, 1, ~~(Date.now() / 1000));
            }

            function waitForImmediate() {
                clientImmediate.BRPOPAsync('message_queue:immediate', 600).then(function(data) {
                    callJob(data);
                    waitForImmediate();
                });
            }

            waitForImmediate();

            setInterval(handleJob, 5000);
            handleJob();
        });
    }
};
