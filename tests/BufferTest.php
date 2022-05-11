<?php

declare(strict_types=1);

use Oct8pus\ByteBuffer\Buffer;
use Oct8pus\ByteBuffer\Endian;
use Oct8pus\ByteBuffer\BufferException;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @covers \Oct8pus\ByteBuffer\Buffer
 */
final class BufferTest extends TestCase
{
    public function setUp() : void
    {
        parent::setUp();
    }

    public function testNoEndianAssertion() : void
    {
        $this->expectException(AssertionError::class);

        $buffer = (new Buffer())
            ->writeWord(0x0000);
    }

    public function testEndian() : void
    {
        $buffer = (new Buffer())
            ->setEndian(Endian::LittleEndian);

        $this->assertEquals(Endian::LittleEndian, $buffer->endian());
    }

    public function testLength() : void
    {
        $buffer = (new Buffer())
            ->setEndian(Endian::LittleEndian);

        $this->assertEquals(0, $buffer->length());

        $buffer->writeChars('12345');

        $this->assertEquals(5, $buffer->length());

        $buffer->truncate();

        $this->assertEquals(0, $buffer->length());
    }

    public function testPosition() : void
    {
        $buffer = (new Buffer())
            ->setEndian(Endian::LittleEndian);

        $this->assertEquals(0, $buffer->position());

        $buffer->writeChars('12345');

        $this->assertEquals(5, $buffer->position());

        $buffer->setPosition(0);

        $this->assertEquals(0, $buffer->position());
    }

    public function testReadWrite() : void
    {
        $buffer = (new Buffer())
            ->setEndian(Endian::LittleEndian)
            ->writeString('Hello')
            ->writeByte(0x01)
            ->writeWord(0xffee)
            ->writeDword(0xaabbccdd)
            ->setPosition(0);

        $this->assertEquals('Hello', $buffer->readString());
        $this->assertEquals(6, $buffer->position());
        $this->assertEquals(0x01, $buffer->readByte());
        $this->assertEquals(7, $buffer->position());
        $this->assertEquals(0xffee, $buffer->readWord());
        $this->assertEquals(9, $buffer->position());
        $this->assertEquals(0xaabbccdd, $buffer->readDword());
    }
}
