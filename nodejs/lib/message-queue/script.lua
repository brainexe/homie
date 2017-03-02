local result = redis.call("ZRANGEBYSCORE", "message_queue:delayed", 0, KEYS[1], "withscores", "LIMIT", 0, 1)
if result == nil or result[1] == nil then
	return nil
else
	local event_id = result[1]
	local result = redis.call("HGET", "message_queue:meta_data", event_id)

	redis.call("ZREM", "message_queue:delayed", event_id)

	return {event_id, result}
end
