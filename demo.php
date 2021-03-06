<?php

use Oct8pus\ByteBuffer\ByteBuffer;
use Oct8pus\ByteBuffer\Endian;
use Oct8pus\ByteBuffer\Origin;

require_once './vendor/autoload.php';

// command line error handler
(new \NunoMaduro\Collision\Provider())->register();

$buffer = (new ByteBuffer())
    ->setEndian(Endian::LittleEndian)
    ->writeString('Hello')
    ->writeString('World')
    ->writeByte(0x07)
    ->writeWord(0xFFFF)
    ->writeDword(0xAABBCCDD);

echo $buffer . "\n";

$buffer
    ->truncate()
    ->writeDword(0x40302010)
    ->writeWord(0x0)
    ->writeWord(0xFFEE)
    ->seek(0, Origin::Start);

echo $buffer . "\n";

printf("%08x\n", $buffer->readDword());
printf("%04x\n", $buffer->readWord());
printf("%04x\n", $buffer->readWord());

$buffer = (new ByteBuffer())
    ->setEndian(Endian::LittleEndian)
    ->writeString('World')
    ->seek(0, Origin::Start)
    ->insertString('Hello');

echo $buffer . "\n";
