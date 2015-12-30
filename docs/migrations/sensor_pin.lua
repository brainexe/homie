-- Script to copy old sensor "pin" to "parameter"

local matches = redis.call('KEYS', 'sensor:*')

for _,key in ipairs(matches) do
    local pin = redis.call('HGET', key, 'pin')
    redis.call('HSET', key, 'parameter', pin)
end
