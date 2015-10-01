
var redis = require("redis"),
    client = redis.createClient(); // todo outsource

const REDIS_MESSAGE_META_DATA = 'todo';
const REDIS_MESSAGE_QUEUE = 'todo';

function randomId() {
    return ~~(Math.random() * 1000000);
}

module.exports.addJob = function (job, timestamp) {
    var eventId = [job.event_name, randomId()].join(':');

    client.HSET(REDIS_MESSAGE_META_DATA, eventId, serialize(job));
    client.ZADD(REDIS_MESSAGE_QUEUE, timestamp, eventId);
};

