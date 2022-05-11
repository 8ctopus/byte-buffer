# byte buffer

A php buffer to work with binary data.

## install

```sh
composer require 8ctopus/byte-buffer
```

## demo

```php
use oct8pus\ByteBuffer\Buffer;

require_once 'vendor/autoload.php';

$router = new Buffer();

```

## run tests

```sh
./vendor/bin/phpunit .
./vendor/bin/phpunit . --coverage-html
```

## clean code

```sh
vendor/bin/php-cs-fixer fix
```
