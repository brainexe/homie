## Integration test:
```
phpunit --testsuite integration
```

## End to end test:
```
npm install -g protractor webdriver-manager
php console user:create testuser testpassword admin
webdriver-manager start
cd test/Frontend
protractor config.js
```

## Code coverage
```
phpunit --coverage-html=coverage
```

With php7 and phpunit via composer:
```
phpdbg  -qrr ~/.composer/vendor/bin/phpunit --coverage-html=coverage
```

