<?php

declare(strict_types=1);

use Oct8pus\ByteBuffer\ByteBuffer;
use Oct8pus\ByteBuffer\Endian;
use Oct8pus\ByteBuffer\BufferException;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @covers \Oct8pus\ByteBuffer\ByteBuffer
 */
final class ByteBufferTest extends TestCase
{
    public function setUp() : void
    {
        parent::setUp();
    }

    public function testNoEndianAssertion() : void
    {
        $this->expectException(AssertionError::class);

        $buffer = (new ByteBuffer())
            ->writeWord(0x0000);
    }

    public function testEndian() : void
    {
        $buffer = (new ByteBuffer())
            ->setEndian(Endian::LittleEndian);

        $this->assertEquals(Endian::LittleEndian, $buffer->endian());
    }

    public function testLength() : void
    {
        $buffer = (new ByteBuffer())
            ->setEndian(Endian::LittleEndian);

        $this->assertEquals(0, $buffer->length());

        $buffer->writeChars('12345');

        $this->assertEquals(5, $buffer->length());

        $buffer->truncate();

        $this->assertEquals(0, $buffer->length());
    }

    public function testPosition() : void
    {
        $buffer = (new ByteBuffer())
            ->setEndian(Endian::LittleEndian);

        $this->assertEquals(0, $buffer->position());

        $buffer->writeChars('12345');

        $this->assertEquals(5, $buffer->position());

        $buffer->setPosition(0);

        $this->assertEquals(0, $buffer->position());
    }

    public function testReadWrite() : void
    {
        $buffer = (new ByteBuffer())
            ->setEndian(Endian::LittleEndian)
            ->writeString('Hello')
            ->writeChars('World')
            ->writeByte(0x01)
            ->writeWord(0xffee)
            ->writeDword(0xaabbccdd)
            ->setPosition(0);

        $this->assertEquals('Hello', $buffer->readString());
        $this->assertEquals('World', $buffer->readChars(5));
        $this->assertEquals(11, $buffer->position());
        $this->assertEquals(0x01, $buffer->readByte());
        $this->assertEquals(12, $buffer->position());
        $this->assertEquals(0xffee, $buffer->readWord());
        $this->assertEquals(14, $buffer->position());
        $this->assertEquals(0xaabbccdd, $buffer->readDword());
    }

    public function testReadStringException() : void
    {
        $this->expectException(AssertionError::class);

        $buffer = (new ByteBuffer())
            ->setEndian(Endian::LittleEndian)
            ->writeChars('Hello')
            ->setPosition(0);

        $buffer->readString();
    }

    public function testInversion() : void
    {
        $buffer = (new ByteBuffer())
            ->setEndian(Endian::LittleEndian)
            ->writeChars('AABBCC');

        $buffer->invert();

        $this->assertEquals('CCBBAA', $buffer->readChars(6));

        $buffer
            ->truncate()
            ->writeDword(0x01020304)
            ->invert();

        $this->assertEquals(0x04030201, $buffer->readDword());
    }
}
