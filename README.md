# byte buffer

Work with binary data in php.

[![Latest Stable Version](http://poser.pugx.org/8ctopus/byte-buffer/v)](https://packagist.org/packages/8ctopus/byte-buffer) [![Total Downloads](http://poser.pugx.org/8ctopus/byte-buffer/downloads)](https://packagist.org/packages/8ctopus/byte-buffer) [![Latest Unstable Version](http://poser.pugx.org/8ctopus/byte-buffer/v/unstable)](https://packagist.org/packages/8ctopus/byte-buffer) [![License](http://poser.pugx.org/8ctopus/byte-buffer/license)](https://packagist.org/packages/8ctopus/byte-buffer) [![PHP Version Require](http://poser.pugx.org/8ctopus/byte-buffer/require/php)](https://packagist.org/packages/8ctopus/byte-buffer)

## install

```sh
composer require 8ctopus/byte-buffer
```

## demo

```php
use Oct8pus\ByteBuffer\ByteBuffer;
use Oct8pus\ByteBuffer\Endian;
use Oct8pus\ByteBuffer\Origin;

require_once 'vendor/autoload.php';

$buffer = (new ByteBuffer())
    ->setEndian(Endian::LittleEndian)
    ->writeString('World')
    ->seek(0, Origin::Start);
    ->insertString('Hello')
    ->seek(0, Origin::End);
    ->writeByte(0x07)
    ->writeWord(0xFFFF)
    ->writeDword(0xAABBCCDD)
    ->seek(0, Origin::Start);

echo $buffer . "\n";

echo $buffer->readString();
echo $buffer->readString();
echo $buffer->readByte();
echo $buffer->readWord();
echo $buffer->readDword();
```

```txt
hex (19):
48656c6c 6f00576f 726c6400 07ffffdd ccbbaa - Hello.World........
```

## run tests

```sh
vendor/bin/phpunit

# code coverage
vendor/bin/phpunit --coverage-html coverage
```

## clean code

```sh
vendor/bin/php-cs-fixer fix
```

# reference

https://igor.io/2012/09/24/binary-parsing.html
