# Sensors

 - Barometer	BMP085
 - Brightness	Webcam
 - Decibel	Webcam
 - Humid	Absolute
 - Humid	DHT11
 - Misc	Metawear
 - Misc	Script
 - System	DiskUsedPercent
 - System	DiskUsed
 - System	Load
 - System	MemoryUsed
 - System	Redis
 - Temperature	DHT11
 - Temperature	DS18
 - Temperature	OnBoard

```
ls src/Homie/Sensors/Sensors/*/*.php | awk -F "/" '{print $5 "\t" substr($6, 0, length($6)-3)}'
```

## DS18 (DS18S20)
1-wire based temperature sensor

### Resources
- [webshed.org](http://webshed.org/wiki/RaspberryPI_DS1820)
- [www.kompf.de - DE](https://www.kompf.de/weather/pionewiremini.html)
- [www.netzmafia.de - DE](http://www.netzmafia.de/skripten/hardware/RasPi/Projekt-Onewire/index.html)
