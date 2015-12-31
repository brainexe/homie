
local matches = redis.call('HGETALL', 'expressions')

for idx = 1, #matches, 2 do
    local key = matches[idx]
    local value = matches[idx + 1]

    value = value:gsub('O:26:\"BrainExe\\Expression\\Entity', 'O:23:\"Homie\\Expression\\Entity')
    redis.call('HSET', 'expressions', key, value)
end
