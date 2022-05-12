<?php

use Oct8pus\ByteBuffer\ByteBuffer;
use Oct8pus\ByteBuffer\Endian;

require_once './vendor/autoload.php';

// command line error handler
(new \NunoMaduro\Collision\Provider())->register();

$buffer = (new ByteBuffer())
    ->setEndian(Endian::LittleEndian)
    ->writeString('Hello')
    ->writeString('World');

echo $buffer . "\n";
echo $buffer[0] ."\n";
echo $buffer[1] ."\n";

$buffer = (new ByteBuffer())
    ->setEndian(Endian::LittleEndian)
    ->writeDword(0x40302010)
    ->writeWord(0x0)
    ->writeWord(0xffee)
    ->writeString('Marc')
    ->writeString('David')
    ->writeChars('Monbaron');

echo $buffer . "\n";

$buffer->setPosition(0);

printf("%08x\n", $buffer->readDword());
printf("%08x\n", $buffer->readWord());
printf("%08x\n", $buffer->readWord());
echo $buffer->readString() ."\n";
echo $buffer->readString() ."\n";
echo $buffer->readChars(7) ."\n";

$buffer->truncate();

$buffer->setEndian(Endian::LittleEndian);
$buffer->writeDword(0x40302010);
$buffer->writeWord(0x0);
$buffer->writeWord(0xffee);
echo $buffer;

$buffer->setPosition(0);

printf("%08x\n", $buffer->readDword());
printf("%08x\n", $buffer->readWord());
printf("%08x\n", $buffer->readWord());
