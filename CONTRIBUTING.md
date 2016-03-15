#Before submitting a pull request

## Refer the [style guide](/../../wiki/Code-style) 

Check PSR-2 styling with
```
vendor/bin/phpcs --standard=PSR2 src/ test/
```

## Run the unit tests
```
vendor/bin/phpunit
```

## Run the functional tests (requires either the Vagrant box or local database setup)
```
APP_ENV=test vendor/bin/phpunit test/functional/
```

## Make sure your code has tests (if applicable)
