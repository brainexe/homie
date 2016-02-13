var http = require('http');
var IRW  = require('infrared').irw;
var irw  = new IRW();

var regexp = /[0-9a-f]+? ([\d]+?) ([a-z_]+?) [a-z_]+/gi;

irw.on('stdout', function(data) {
    var match = regexp.exec(data);

    if (match && match[1] == '00') {
        var code = match[2];

        console.log(code);

        console.log({
            host: 'localhost',
            path: '/remote/receive/' + code + '/'
        });

        http.request({
            host: 'localhost',
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
irw.start();
