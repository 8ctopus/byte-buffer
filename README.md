# byte buffer

[![packagist](https://poser.pugx.org/8ctopus/byte-buffer/v)](https://packagist.org/packages/8ctopus/byte-buffer)
[![downloads](https://poser.pugx.org/8ctopus/byte-buffer/downloads)](https://packagist.org/packages/8ctopus/byte-buffer)
[![min php version](https://poser.pugx.org/8ctopus/byte-buffer/require/php)](https://packagist.org/packages/8ctopus/byte-buffer)
[![license](https://poser.pugx.org/8ctopus/byte-buffer/license)](https://packagist.org/packages/8ctopus/byte-buffer)
[![tests](https://github.com/8ctopus/byte-buffer/actions/workflows/tests.yml/badge.svg)](https://github.com/8ctopus/byte-buffer/actions/workflows/tests.yml)
![code coverage badge](https://raw.githubusercontent.com/8ctopus/byte-buffer/image-data/coverage.svg)
![lines of code](https://raw.githubusercontent.com/8ctopus/byte-buffer/image-data/lines.svg)

A php buffer to work with binary data.

## install

    composer require 8ctopus/byte-buffer

## demo

```php
use Oct8pus\ByteBuffer\ByteBuffer;
use Oct8pus\ByteBuffer\Endian;
use Oct8pus\ByteBuffer\Origin;

require_once __DIR__ . '/vendor/autoload.php';

echo "Let's create a new little endian buffer and write string Hello World\n";

$buffer = (new ByteBuffer())
    ->setEndian(Endian::LittleEndian)
    ->writeString('Hello World');

echo $buffer . "\n";
// hex (12/12): 48656c6c 6f20576f 726c6400 - Hello World.

echo "Add byte 0x07, word 0xFFFF and dword 0xAABBCCDD\n";

$buffer
    ->writeByte(0x07)
    ->writeWord(0xFFFF)
    ->writeDword(0xAABBCCDD);

echo $buffer;
// hex (19/19): 48656c6c 6f20576f 726c6400 07ffffdd ccbbaa - Hello World........

echo "\nSeek buffer back to origin\n";

$buffer->seek(0, Origin::Start);

echo $buffer;
// hex (0/19): 48656c6c 6f20576f 726c6400 07ffffdd ccbbaa - Hello World........

echo "\nRead string from buffer\n";

echo $buffer->readString() . "\n";
// Hello World

echo "\nRead byte, word and dword\n";

printf("0x%02X\n", $buffer->readByte());
printf("0x%04X\n", $buffer->readword());
printf("0x%08X\n", $buffer->readDword());
// 0x07
// 0xFFFF
// 0xAABBCCDD

echo "\nDelete World from buffer\n";

$buffer->delete(6, 5);

echo $buffer;
// hex (19/14): 48656c6c 6f200007 ffffddcc bbaa - Hello ........

echo "\nCalculate buffer crc32b\n";

echo '0x'. strtoupper($buffer->crc32b(true)) . "\n";
// 0xF3B2604E

echo "\nInsert Parrot at position 6\n";

$buffer
    ->seek(6, Origin::Start)
    ->insertChars('Parrot');

echo $buffer;
// hex (12/20): 48656c6c 6f205061 72726f74 0007ffff ddccbbaa - Hello Parrot........

echo "\nCopy Parrot to a new buffer\n";

$parrot = $buffer->copy(6, 6);

echo $parrot;
// hex (0/6): 50617272 6f74 - Parrot

echo "\nInvert Parrot\n";

echo $parrot->invert();
// hex (0/6): 746f7272 6150 - torraP

echo "\nCalculate hashes\n";
echo 'md5: ' . $parrot->md5(false) . "\n";
echo 'sha1: ' . $parrot->sha1(false) . "\n";
echo 'sha256: ' . $parrot->sha256(false) . "\n";
// md5: 4264ed2a05f9548fb3b26601c1c904c4
// sha1: d50da4682cdb41c803075168f5c132fd33ffa34d
// sha256: e2d6ddb19ca9c3396521297f4a09581c1187acb29931c8d4431941be71d2215c

echo "\nTruncate buffer\n";

echo $parrot->truncate();
// hex (0/0):
```

## run tests

    composer test

## clean code

    composer fix
    composer fix-risky

# reference

[https://igor.io/2012/09/24/binary-parsing.html](https://igor.io/2012/09/24/binary-parsing.html)
