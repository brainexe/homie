# Conditions

# Actions


# Functions
```
php console expression:list
```

## Native PHP functions
 - sprintf(...) 
 - date(string[, int]) 
 - time()
 - microtime([bool])
 - rand([number, number])
 - round(number)
 
## Dealing with events
 - isEvent(string $eventName)
 - isTiming(string $timingId) // check for timing/cron event
 - event(string EventName, ...parameters) // throw an internal event

## Store data 
 - setProperty(key, value)
 - getProperty(key)
 - increaseCounter()  
 
## Sensor
 - getSensorValue(int $sensorId)
 - getSensor(int $sensorId)
  
## Misc
 - say(string $text)
 - exec(string $inputControl)
 - log(string $level, string $message, $context = null)
 - executeExpression TODO

# Examples

## Motion detector
Enabled between 2AM and 7AM
```
Condition: isEvent("motion.motion") && date('G') > 2 && date('G') <= 6
Actions: 
 - exec('send mail "yourname@example.com" "Motion detected in your flat" "Motion detected in your flat"')
 - exec('webcam video 10 seconds')
 - exec("say Go away, police is called")
 - exec('play sound egg_timer.mp3')
```


motion detection
tbd

# Events
http://homie/expressions/

# "Input control" (deprecated)
 - send mail "(.*)" "(.*)" "(.*)"
 - radio (on|off) (\s+)
 - switch (on|off) (\s+)
 - (say|speak) (.*)
 - webcam
 - webcam video (\d+) seconds
 - add shopping item (.*)
 - delete shopping item (.*)
 - add item (.*)
 - delete item (\d+)
 - assign item (\d+) to (\s+)
 - set item status (\d+) to (\d+)
 - todo list
 - play sound (.*)
 - sensor say (\d+)
 - echo (.*)

php console input:list
```
