<?php

declare(strict_types=1);

use NunoMaduro\Collision\Provider;
use Oct8pus\ByteBuffer\ByteBuffer;
use Oct8pus\ByteBuffer\Endian;
use Oct8pus\ByteBuffer\Origin;

require_once __DIR__ . '/vendor/autoload.php';

// command line error handler
(new Provider())->register();

echo "Let's create a new little endian buffer and write string Hello World\n";

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

echo '0x' . strtoupper($buffer->crc32b(true)) . "\n";

echo "\nInsert Parrot at position 6\n";

$buffer
    ->seek(6, Origin::Start)
    ->insertChars('Parrot');

echo $buffer;

echo "\nCopy Parrot to a new buffer\n";

$parrot = $buffer->copy(6, 6);

echo $parrot;

echo "\nInvert Parrot\n";

echo $parrot->invert();

echo "\nCalculate hashes\n";
echo 'md5: ' . $parrot->md5(false) . "\n";
echo 'sha1: ' . $parrot->sha1(false) . "\n";
echo 'sha256: ' . $parrot->sha256(false) . "\n";

echo "\nTruncate buffer\n";

echo $parrot->truncate();
