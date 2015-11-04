
var redis = require("redis"),
    client = redis.createClient(); // todo outsource

const REDIS_MESSAGE_QUEUE     = 'message_queue';
const REDIS_MESSAGE_META_DATA = 'message_queue_meta_data';

function randomId() {
    return ~~(Math.random() * 10000000);
}

module.exports.addJob = function (job, timestamp) {
    var eventId = [job.event_name, randomId()].join(':');

    client.HSET(REDIS_MESSAGE_META_DATA, eventId, serialize(job));
    client.ZADD(REDIS_MESSAGE_QUEUE, timestamp, eventId);
};

