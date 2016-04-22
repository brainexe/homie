var http   = require('http');
var common = require('./lib/common');
var config = require('./lib/config');
var IRW    = require('infrared').irw;
var irw    = new IRW();

common.changeUser();

var regexp = /[0-9a-f]+? ([\d]+?) ([a-z_]+?) [a-z_]+/gi;

irw.on('stdout', function(data) {
    var match = regexp.exec(data);

    if (match && match[1] == '00') {
        var code = match[2];

        console.log(code);

        console.log({
            host: config['server.host'],
            path: '/remote/receive/' + code + '/'
        });

        http.request({
            host: config['server.host'],
            path: '/remote/receive/' + code + '/'
        }, function (response) {
            var str = '';
            response.on('data', function (chunk) {
                str += chunk;
            });
            response.on('end', function () {
                console.log(str);
            });
        });
    }
});

console.log('Started process');

irw.start();
