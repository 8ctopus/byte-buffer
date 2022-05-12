<?php

declare(strict_types=1);

use Oct8pus\ByteBuffer\ByteBuffer;
use Oct8pus\ByteBuffer\ByteBufferException;
use Oct8pus\ByteBuffer\Endian;
use Oct8pus\ByteBuffer\Origin;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @covers \Oct8pus\ByteBuffer\ByteBuffer
 */
final class ByteBufferTest extends TestCase
{
    public function testNoEndianException() : void
    {
        $buffer = new ByteBuffer();

        $count = 0;

        try {
            $buffer->readWord();
        } catch (Exception $e) {
            ++$count;
        }

        try {
            $buffer->readDWord();
        } catch (Exception $e) {
            ++$count;
        }

        try {
            $buffer->writeWord(0x0000);
        } catch (Exception $e) {
            ++$count;
        }

        try {
            $buffer->writeDWord(0x0000);
        } catch (Exception $e) {
            ++$count;
        }

        $this->assertEquals(4, $count);
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

    public function testSeekAndPosition() : void
    {
        $buffer = (new ByteBuffer())
            ->setEndian(Endian::LittleEndian);

        $this->assertEquals(0, $buffer->position());

        $str = '12345';

        $buffer->writeChars($str);

        $this->assertEquals(strlen($str), $buffer->position());

        $buffer->seek(0, Origin::Start);

        $this->assertEquals(0, $buffer->position());

        $buffer->seek(0, Origin::End);

        $this->assertEquals(strlen($str), $buffer->position());

        $buffer->seek(-2, Origin::Current);

        $this->assertEquals(strlen($str) - 2, $buffer->position());

        $buffer->seek(+2, Origin::Current);

        $this->assertEquals(strlen($str), $buffer->position());

        $this->expectException(ByteBufferException::class);

        $buffer->seek(-1, Origin::Start);
    }

    public function testReadWriteLE() : void
    {
        $buffer = (new ByteBuffer())
            ->setEndian(Endian::LittleEndian)
            ->writeString('Hello')
            ->writeChars('World')
            ->writeByte(0x01)
            ->writeWord(0xffee)
            ->writeDword(0xaabbccdd)
            ->seek(0, Origin::Start);

        $this->assertEquals('Hello', $buffer->readString());
        $this->assertEquals('World', $buffer->readChars(5));
        $this->assertEquals(11, $buffer->position());
        $this->assertEquals(0x01, $buffer->readByte());
        $this->assertEquals(12, $buffer->position());
        $this->assertEquals(0xffee, $buffer->readWord());
        $this->assertEquals(14, $buffer->position());
        $this->assertEquals(0xaabbccdd, $buffer->readDword());

        $buffer
            ->seek(-7, Origin::Current)
            ->writeByte(0x11)
            ->writeWord(0x7766)
            ->writeDword(0xffeeddaa)
            ->seek(-7, Origin::End);

        $this->assertEquals(0x11, $buffer->readByte());
        $this->assertEquals(0x7766, $buffer->readWord());
        $this->assertEquals(0xffeeddaa, $buffer->readDword());
    }

    public function testReadWriteBE() : void
    {
        $buffer = (new ByteBuffer())
            ->setEndian(Endian::BigEndian)
            ->writeString('Hello')
            ->writeChars('World')
            ->writeByte(0x01)
            ->writeWord(0xffee)
            ->writeDword(0xaabbccdd)
            ->seek(0, Origin::Start);

        $this->assertEquals('Hello', $buffer->readString());
        $this->assertEquals('World', $buffer->readChars(5));
        $this->assertEquals(11, $buffer->position());
        $this->assertEquals(0x01, $buffer->readByte());
        $this->assertEquals(12, $buffer->position());
        $this->assertEquals(0xffee, $buffer->readWord());
        $this->assertEquals(14, $buffer->position());
        $this->assertEquals(0xaabbccdd, $buffer->readDword());

        $buffer
            ->seek(-7, Origin::Current)
            ->writeByte(0x11)
            ->writeWord(0x7766)
            ->writeDword(0xffeeddaa)
            ->seek(-7, Origin::End);

        $this->assertEquals(0x11, $buffer->readByte());
        $this->assertEquals(0x7766, $buffer->readWord());
        $this->assertEquals(0xffeeddaa, $buffer->readDword());
    }

    public function testReadStringException() : void
    {
        $this->expectException(ByteBufferException::class);

        $buffer = (new ByteBuffer())
            ->setEndian(Endian::LittleEndian)
            ->writeChars('Hello')
            ->seek(0, Origin::Start);

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

    public function testArrayAccess() : void
    {
        $str = 'abcdef';

        $buffer = (new ByteBuffer())
            ->setEndian(Endian::LittleEndian)
            ->writeChars('------');

        // set offsets
        for ($i = 0; $i < strlen($str); ++$i) {
            $buffer[$i] = $str[$i];
        }

        // get offsets
        for ($i = 0; $i < strlen($str); ++$i) {
            $this->assertEquals(ord($str[$i]), $buffer[$i]);
        }

    }

    public function testArrayAccess2() : void
    {
        $str = 'abcdef';

        $buffer = (new ByteBuffer())
            ->setEndian(Endian::LittleEndian)
            ->writeChars($str);

        $this->expectException(ByteBufferException::class);

        $buffer[strlen($str)];
    }

    public function testArrayAccess3() : void
    {
        $str = 'abcdef';

        $buffer = (new ByteBuffer())
            ->setEndian(Endian::LittleEndian)
            ->writeChars($str);

        $this->expectException(ByteBufferException::class);

        $buffer[strlen($str)] = ord('a');
    }

    public function testArrayAccess4() : void
    {
        $buffer = (new ByteBuffer())
            ->setEndian(Endian::LittleEndian);

        $this->expectException(ByteBufferException::class);

        $buffer['a'];
    }

    public function testArrayAccess5() : void
    {
        $buffer = (new ByteBuffer())
            ->setEndian(Endian::LittleEndian);

        $this->expectException(ByteBufferException::class);

        $buffer['a'] = ord('a');
    }

    public function testArrayAccessOffsetExists() : void
    {
        $str = 'abcdef';

        $buffer = (new ByteBuffer())
            ->setEndian(Endian::LittleEndian)
            ->writeChars($str);

        $i = 0;

        for (; $i < strlen($str); ++$i) {
            $this->assertTrue(isset($buffer[$i]));
        }

        $this->assertFalse(isset($buffer[$i]));

        $this->expectException(ByteBufferException::class);

        isset($buffer['a']);
    }

    public function testCastToString() : void
    {
        $buffer = (new ByteBuffer())
            ->setEndian(Endian::LittleEndian)
            ->writeChars('abcdef');

        $this->assertEquals("hex (6): \n61626364 6566 - abcdef\n", (string) $buffer);
    }

    public function testStringMethod() : void
    {
        $str = 'abcdef';

        $buffer = (new ByteBuffer())
            ->setEndian(Endian::LittleEndian)
            ->writeChars($str);

        $this->assertEquals($str, $buffer->string());
    }

    public function testCrc() : void
    {
        $str = 'abcdefghijklmnopqrstuvwxyz0123456789';

        $buffer = (new ByteBuffer())
            ->setEndian(Endian::LittleEndian)
            ->writeChars($str);

        $this->assertEquals(hash('crc32b', $str, false), $buffer->crc32b(true));
    }
}
