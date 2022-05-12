# byte buffer

Work with binary data in php.

## install

```sh
composer require 8ctopus/byte-buffer
```

## demo

```php
use Oct8pus\ByteBuffer\ByteBuffer;
use Oct8pus\ByteBuffer\Endian;

require_once 'vendor/autoload.php';

$buffer = (new ByteBuffer())
    ->setEndian(Endian::LittleEndian)
    ->writeString('Hello')
    ->writeString('World')
    ->writeWord(0xffff)
    ->writeDword(0xaabbccdd);

echo $buffer . "\n";
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
