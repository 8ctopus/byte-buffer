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
    ->writeByte(0x07)
    ->writeWord(0xFFFF)
    ->writeDword(0xAABBCCDD);

echo $buffer . "\n";
```

```txt
hex (19):
48656c6c 6f00576f 726c6400 07ffffdd ccbbaa - Hello.World........
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
