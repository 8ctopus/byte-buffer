# byte buffer

Work with binary data in php.

[![latest stable version](http://poser.pugx.org/8ctopus/byte-buffer/v)](https://packagist.org/packages/8ctopus/byte-buffer)
[![tests](https://github.com/8ctopus/byte-buffer/actions/workflows/tests.yml/badge.svg)](https://github.com/8ctopus/byte-buffer/actions/workflows/tests.yml)
![code coverage badge](./coverage.svg)
[![downloads](http://poser.pugx.org/8ctopus/byte-buffer/downloads)](https://packagist.org/packages/8ctopus/byte-buffer)
[![min php version](http://poser.pugx.org/8ctopus/byte-buffer/require/php)](https://packagist.org/packages/8ctopus/byte-buffer)
[![license](http://poser.pugx.org/8ctopus/byte-buffer/license)](https://packagist.org/packages/8ctopus/byte-buffer)

## install

    composer require 8ctopus/byte-buffer

## demo

```php
use Oct8pus\ByteBuffer\ByteBuffer;
use Oct8pus\ByteBuffer\Endian;
use Oct8pus\ByteBuffer\Origin;

require_once 'vendor/autoload.php';

echo "Let's create a new little endian ByteBuffer and write Hello World\n";

$buffer = (new ByteBuffer())
    ->setEndian(Endian::LittleEndian)
    ->writeString('Hello World');

echo $buffer . "\n";

echo "Add byte 0x07, word 0xFFFF and dword 0xAABBCCDD\n";

$buffer
    ->writeByte(0x07)
    ->writeWord(0xFFFF)
    ->writeDword(0xAABBCCDD);

echo $buffer;

echo "\nSeek buffer back to origin\n";

$buffer->seek(0, Origin::Start);

echo $buffer;

echo "\nRead string from buffer\n";

echo $buffer->readString() . "\n";

echo "\nRead byte, word and dword\n";

printf("0x%02X\n", $buffer->readByte());
printf("0x%04X\n", $buffer->readword());
printf("0x%08X\n", $buffer->readDword());

echo "\nDelete World from buffer\n";

$buffer->delete(6, 5);

echo $buffer;

echo "\nCalculate buffer crc32b\n";

echo '0x'. strtoupper($buffer->crc32b(true)) . "\n";

echo "\nInsert Parrot at position 6\n";

$buffer
    ->seek(6, Origin::Start)
    ->insertChars('Parrot');

echo $buffer;

echo "\nCopy Parrot into a new buffer\n";

$parrot = $buffer->copy(6, 6);

echo $parrot;

echo "\nInvert Parrot\n";

echo $parrot->invert();
```

## run tests

    composer test

## clean code

    composer fix
    composer fix-risky

# reference

    https://igor.io/2012/09/24/binary-parsing.html
