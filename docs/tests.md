## Tests

### PHP Static Analysis Tools
PHP Codesniffer
```bash
$ ./vendor/bin/phpcs --standard=phpcs.xml -n -p src/
$ ./vendor/bin/phpcbf --standard=phpcs.xml -p src/*
```

[PHPStan](https://github.com/phpstan/phpstan)
```bash
$ ./vendor/bin/phpstan analyse -l 4 -c phpstan.neon src/
```
PHP Insights [https://phpinsights.com/](https://phpinsights.com/)
```bash
$ ./vendor/bin/phpinsights
```

### PHPUnit tests / [phpunit.readthedocs.io](https://phpunit.readthedocs.io)

```bash
$ php ./vendor/bin/phpunit tests/unit -v --coverage-text --colors=never --stderr --stop-on-failure
```

### Codeception tests / [codeception.com](https://codeception.com/)
```bash
$ php bin/console cache:clear --env=test
$ ./vendor/bin/codecept run acceptance --steps --xml --html
```