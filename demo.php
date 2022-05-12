<?php

use Oct8pus\ByteBuffer\ByteBuffer;
use Oct8pus\ByteBuffer\Endian;

require_once './vendor/autoload.php';

// command line error handler
(new \NunoMaduro\Collision\Provider())->register();

$buffer = (new ByteBuffer())
    ->setEndian(Endian::LittleEndian)
    ->writeString('Hello')
    ->writeString('World')
    ->writeByte(0x07)
    ->writeWord(0xffff)
    ->writeDword(0xaabbccdd);

echo $buffer . "\n";

$buffer
    ->truncate()
    ->writeDword(0x40302010)
    ->writeWord(0x0)
    ->writeWord(0xffee)
    ->setPosition(0);

echo $buffer . "\n";

printf("%08x\n", $buffer->readDword());
printf("%04x\n", $buffer->readWord());
printf("%04x\n", $buffer->readWord());
