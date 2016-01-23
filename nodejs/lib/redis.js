var redis    = require('redis'),
    bluebird = require('bluebird');

var client;
var clients = {};

bluebird.promisifyAll(redis.RedisClient.prototype);
bluebird.promisifyAll(redis.Multi.prototype);

module.exports = {
    getClient: function(name) {
        if (clients[name]) {
            return clients[name];
        }

        client = clients[name] = redis.createClient();

        // todo auth + select DB

        return client;
    }
};
