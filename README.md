# byte buffer

A php buffer to work with binary data.

## install

```sh
composer require 8ctopus/byte-buffer
```

## demo

```php
use oct8pus\ByteBuffer\ByteBuffer;

require_once 'vendor/autoload.php';

$router = new ByteBuffer();

```

## run tests

```sh
./vendor/bin/phpunit .
./vendor/bin/phpunit . --coverage-html coverage
```

## clean code

```sh
vendor/bin/php-cs-fixer fix
```
