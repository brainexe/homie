# Amazon Dash

## /etc/cron.d/homie
```
@reboot root sleep 20 && amazon-dash --config /opt/amazon-dash.yml --debug run >> /www/homie/logs/dash.log
```

## /opt/amazon-dash.yml
```
settings:
  delay: 10
devices:
  44:65:1d:43:33:8f:
    name: Matze
    user: pi
    cmd: curl "http://raspberrypi/ifttt/?event=dash&accessToken=6sx5bgqjyjggwwo4kk"
```
