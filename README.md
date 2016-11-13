[![Build Status](https://travis-ci.org/brainexe/homie.png?branch=master)](https://travis-ci.org/brainexe/homie)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/brainexe/homie/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/brainexe/homie/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/brainexe/homie/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/brainexe/homie/?branch=master)
[![Dependency Status](https://www.versioneye.com/user/projects/5669f01243cfea003100019c/badge.svg?style=flat)](https://www.versioneye.com/user/projects/5669f01243cfea003100019c)

# Overview
Homie is a software to automate you home using low-budget hardware, like a RaspberryPi.

# Requirements
 - PHP 7.0/7.1
 - nodejs 4.0+
 - redis-server

# Installation
  - composer install
  - npm install
  - grunt exec:install
  - ./console user:create "user" "pAsSworD" "admin" # creates in initial user "user" with admin role and the given password
  - ./console server:run # runs the build-in PHP webserver on port 8080

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
- AngularJS single page application
- Mobile/Chrome App
- ... tbd

# Screenshots
tdb...
![Dashboard](https://space.mdoetsch.de/index.php/s/6mMnrHBeIitEHSa/download)
![Sensor](https://space.mdoetsch.de/index.php/s/Gib5tiNJ26mWICh/download)
![Sensor List](https://space.mdoetsch.de/index.php/s/XD18BpS3aoSbbZb/download)

