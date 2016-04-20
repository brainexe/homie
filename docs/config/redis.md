Just use the [Redis URI Schema|http://www.iana.org/assignments/uri-schemes/prov/redis]

Default local connection:
```redis.connection: "redis://127.0.0.1/"```

Remote server on database "4"
```redis.connection: "tcp://10.0.0.1/4"```

Use local redis instance as readonly slave. "redis.connection" is used for any write.
```redis.slave.connection: "redis://127.0.0.1/"```
