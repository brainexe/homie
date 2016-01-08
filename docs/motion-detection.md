# External motion detection
If you want to trigger a motion event, make an GET/POST HTTP request to your "/motion/add/" path. This will trigger an MotionEvent (motion.motion)

## Installation of "motion"
http://www.lavrsen.dk/foswiki/bin/view/Motion/

## Configuration
Edit file "/etc/motion/motion.conf"
```
on_motion_detected curl -X GET http://homie/motion/add/
```

## Run
```
/etc/init.d/motion start
```

## Usage as Trigger
```
isEvent("motion.motion")
```
