[![Build Status](https://travis-ci.org/brainexe/homie.png?branch=master)](https://travis-ci.org/brainexe/homie)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/brainexe/homie/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/brainexe/homie/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/brainexe/homie/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/brainexe/homie/?branch=master)
[![Dependency Status](https://www.versioneye.com/user/projects/5669f01243cfea003100019c/badge.svg?style=flat)](https://www.versioneye.com/user/projects/5669f01243cfea003100019c)

# Overview
Homie is a software to automate you home using low-budget hardware, like a RaspberryPi.

# Requirements
 - PHP 7.1
 - nodejs 4.0+
 - redis-server

# Installation
  - git clone git@github.com:brainexe/homie.git
  - composer install
  - npm install
  - grunt exec:install
  - ./console user:create "user" "pAsSworD" "admin" # creates in initial user "user" with admin role and the given password
  - ./console server:run # run the build-in PHP webserver on http://localhost:8080

# Tests
## Unit tests:
```
phpunit --testsuite unit
```

## Integration test:
```
phpunit --testsuite integration
```

## End to end test:
```
php console user:create testuser testpassword admin
./node_modules/protractor/bin/webdriver-manager update

npm run-script protractor
```

# Features
- Many sensors supported, like temperature, barometer, light sensor etc.
- Real time websocket notifications
- Dynamic rule system with connection to IFTTT, openHAB...
- AngularJS single page application with realtime notifications
- Mobile/Chrome App
- Motion detection
- ... tbd

# Screenshots
### Dashboard
![Dashboard](https://github.com/brainexe/homie/raw/master/docs/images/dashboard.png)
### Sensors
![Sensor](https://github.com/brainexe/homie/raw/master/docs/images/sensor.png)
![Sensor List](https://github.com/brainexe/homie/raw/master/docs/images/sensor_list.png)
### Switches
![Switches List](https://github.com/brainexe/homie/raw/master/docs/images/switches.png)
### Rules
![Rules](https://github.com/brainexe/homie/raw/master/docs/images/expression_list.png)
tdb: add more and newer screenshots
