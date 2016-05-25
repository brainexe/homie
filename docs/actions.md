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
 - sleep(number)
 - preg_match()
 - constant
 
## Dealing with events
 - isEvent(string $eventName)
 - isTiming(string $timingId) // check for timing/cron event
 - event(string EventName, ...parameters) // dispatch an internal event

## Store data 
 - setProperty(key, value)
 - getProperty(key)
 - increaseCounter()

## Sensor
 - getSensorValue(int $sensorId)
 - getSensor(int $sensorId)
 - isSensorValue(int $sensorId)
  
## Misc
 - say(string $text)
 - sendMail(recipientMail, subject, body)
 - exec(string $inputControl)
 - log(string $level, string $message, $context = null)
 - executeExpression TODO
 - triggerIFTTT(eventId)
 - takePhoto()
 - takeVideo()
 - addShoppingListItem()
 - sayTodoList()
 - removeShoppingListItem()
 - setSwitch(witchId, status)
 - voice() voice control
 - eggTimer(time, text)
 - isRemoteCode()

# Examples

## Motion detector
Enabled between 2AM and 7AM
```
Condition: isEvent("motion.motion") && date('G') > 2 && date('G') <= 6
Actions: 
 - sendMail("yourname@example.com", "Motion detected in your flat", "Motion detected in your flat"')
 - say("Go away, police is called")
 - exec('webcam video 10 seconds')
 - exec('play sound egg_timer.mp3')
```

## Air quality check
Notifies you when the humidity is over 70% by using speakers and sending an notification to IFTTT. This might trigger many other actions...
```
Conditions:
isTiming("15minutes") && getSensorValue(indorrhumidity) >= 70
Actions:
 - say("Humidity is at " ~ round(getSensorValue(indorrhumidity)) ~ " Percent")
 - triggerIFTTT("hightHumidity", round(getSensorValue(indorrhumidity)))
```

# Events
http://homie/expressions/
