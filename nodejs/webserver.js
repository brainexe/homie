var config = require('./lib/config');
var http = require('http');
var url = require('url') ;
var redis   = require('./lib/redis');
var serveStatic = require('serve-static');

var redisPub = redis.getClient('webserver-publish');
var redisSub = redis.getClient('webserver-subscribe');

var port = 6789;
var timeout = 5000;

const SESSION_NAME = 'sid';

var serve = serveStatic(__dirname + '/../web/', {'index': ['index.html']});

function parseCookies (request) {
    var list = {},
        rc = request.headers.cookie;

    rc && rc.split(';').forEach(function( cookie ) {
        var parts = cookie.split('=');
        list[parts.shift().trim()] = decodeURI(parts.join('='));
    });

    return list;
}

var queue = {};

http.createServer(function (req, res) {
    serve(req, res, function() {
        var requestId = ~~(Math.random() * 999999999999); // TODO

        queue[requestId] = res;

        var url_parts = url.parse(req.url, true);

        var body = "";
        req.on('data', function (chunk) {
            body += chunk;
        });

        redisSub.psubscribe('response:*');

        var sessionId = 'pe0isqtcms3j81okm78smnjd20'; // TODO

        req.on('end', function () {
            var request = {
                'requestId': requestId,
                'sessionId': sessionId,
                'get':       url_parts.query,
                'post':      body ? JSON.parse(body) : {}, ////qs.parse(body),
                'request':   {}, // todo ?
                'cookies':   parseCookies(req),
                'files':     {},
                'server': {
                    'REQUEST_URI':      req.url,
                    'REQUEST_METHOD':   req.method,
                    'SERVER_PROTOCOL':  req.protocol,
                    'REMOTE_ADDR':      req.connection.remoteAddress,
                    'REQUEST_TIME_FLOAT': Date.now() / 1000
                },
                'raw':     body,
                'headers': req.headers
            };

            redisPub.lpush('request', JSON.stringify(request));

            setTimeout(function() {
                if (res) {
                    res.end("Timeout :(");
                    delete queue[requestId];
                }
            }, timeout)
        });
    });
}).listen(port);

redisSub.psubscribe('response:*');
redisSub.on('pmessage', function(pattern, channel, message) {
    var res;
    var response = JSON.parse(message);
    if (res = queue[response.requestId]) {
        delete queue[response.requestId];

        res.writeHead(response.status, response.headers);
        res.end(response.body);
    }
});
