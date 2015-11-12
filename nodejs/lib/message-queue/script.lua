local result = redis.call("ZRANGEBYSCORE", "message_queue", 0, KEYS[2], "withscores", "LIMIT", 0, KEYS[1])
if result == nil then
	return nil
else
	local event_id = result[1]
	local result = redis.call("HGET", "message_queue_meta_data", event_id)

	redis.call("ZREM", "message_queue", event_id)

	return {event_id, result}
end
