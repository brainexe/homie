# Installation
tbd
useful documentation: http://cylonjs.com/documentation/platforms/raspberry-pi/

## Used packages
 - espeak

### GPIO/433 switch
 - gpio
 - rcswitch-pi (todo)

### Webcam
 - fswebcam
 - arecord
 - streamer
 - raspistill

### i2c display:
 - i2c-tools


# GPIO without root
In order to access the GPIO pins without using sudo you will need to both app the pi user to the gpio group:

```
sudo usermod -G gpio pi
```

## I2C Display
https://github.com/sweetpi/python-i2c-lcd
