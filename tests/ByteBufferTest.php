<?php

declare(strict_types=1);

use Oct8pus\ByteBuffer\ByteBuffer;
use Oct8pus\ByteBuffer\ByteBufferException;
use Oct8pus\ByteBuffer\Endian;
use Oct8pus\ByteBuffer\Origin;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
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
        } catch (ByteBufferException) {
            ++$count;
        }

        try {
            $buffer->readDWord();
        } catch (ByteBufferException) {
            ++$count;
        }

        try {
            $buffer->writeWord(0x0000);
        } catch (ByteBufferException) {
            ++$count;
        }

        try {
            $buffer->writeDWord(0x0000);
        } catch (ByteBufferException) {
            ++$count;
        }

        self::assertSame(4, $count);
    }

    public function testEndian() : void
    {
        $buffer = (new ByteBuffer())
            ->setEndian(Endian::LittleEndian);

        self::assertSame(Endian::LittleEndian, $buffer->endian());
    }

    public function testLength() : void
    {
        $buffer = (new ByteBuffer())
            ->setEndian(Endian::LittleEndian);

        self::assertSame(0, $buffer->length());

        $buffer->writeChars('12345');

        self::assertSame(5, $buffer->length());

        $buffer->truncate();

        self::assertSame(0, $buffer->length());
    }

    public function testSeekAndPosition() : void
    {
        $buffer = (new ByteBuffer())
            ->setEndian(Endian::LittleEndian);

        self::assertSame(0, $buffer->position());

        $str = '12345';

        $buffer->writeChars($str);

        self::assertSame(strlen($str), $buffer->position());

        $buffer->seek(0, Origin::Start);

        self::assertSame(0, $buffer->position());

        $buffer->seek(0, Origin::End);

        self::assertSame(strlen($str), $buffer->position());

        $buffer->seek(-2, Origin::Current);

        self::assertSame(strlen($str) - 2, $buffer->position());

        $buffer->seek(+2, Origin::Current);

        self::assertSame(strlen($str), $buffer->position());

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
            ->writeWord(0xFFEE)
            ->writeDword(0xAABBCCDD)
            ->seek(0, Origin::Start);

        self::assertSame('Hello', $buffer->readString());
        self::assertSame('World', $buffer->readChars(5));
        self::assertSame(11, $buffer->position());
        self::assertSame(0x01, $buffer->readByte());
        self::assertSame(12, $buffer->position());
        self::assertSame(0xFFEE, $buffer->readWord());
        self::assertSame(14, $buffer->position());
        self::assertSame(0xAABBCCDD, $buffer->readDword());

        $buffer
            ->seek(-7, Origin::Current)
            ->writeByte(0x11)
            ->writeWord(0x7766)
            ->writeDword(0xFFEEDDAA)
            ->seek(-7, Origin::End);

        self::assertSame(0x11, $buffer->readByte());
        self::assertSame(0x7766, $buffer->readWord());
        self::assertSame(0xFFEEDDAA, $buffer->readDword());
    }

    public function testReadWriteBE() : void
    {
        $buffer = (new ByteBuffer())
            ->setEndian(Endian::BigEndian)
            ->writeString('Hello')
            ->writeChars('World')
            ->writeByte(0x01)
            ->writeWord(0xFFEE)
            ->writeDword(0xAABBCCDD)
            ->seek(0, Origin::Start);

        self::assertSame('Hello', $buffer->readString());
        self::assertSame('World', $buffer->readChars(5));
        self::assertSame(11, $buffer->position());
        self::assertSame(0x01, $buffer->readByte());
        self::assertSame(12, $buffer->position());
        self::assertSame(0xFFEE, $buffer->readWord());
        self::assertSame(14, $buffer->position());
        self::assertSame(0xAABBCCDD, $buffer->readDword());

        $buffer
            ->seek(-7, Origin::Current)
            ->writeByte(0x11)
            ->writeWord(0x7766)
            ->writeDword(0xFFEEDDAA)
            ->seek(-7, Origin::End);

        self::assertSame(0x11, $buffer->readByte());
        self::assertSame(0x7766, $buffer->readWord());
        self::assertSame(0xFFEEDDAA, $buffer->readDword());
    }

    public function testInserts() : void
    {
        $buffer = (new ByteBuffer())
            ->setEndian(Endian::BigEndian)
            ->insertDword(0xAABBCCDD)
            ->seek(0, Origin::Start)
            ->insertWord(0xFFEE)
            ->seek(0, Origin::Start)
            ->insertByte(0x01)
            ->seek(0, Origin::Start)
            ->insertChars('World')
            ->seek(0, Origin::Start)
            ->insertString('Hello')
            ->seek(0, Origin::Start);

        self::assertSame('Hello', $buffer->readString());
        self::assertSame('World', $buffer->readChars(5));
        self::assertSame(11, $buffer->position());
        self::assertSame(0x01, $buffer->readByte());
        self::assertSame(12, $buffer->position());
        self::assertSame(0xFFEE, $buffer->readWord());
        self::assertSame(14, $buffer->position());
        self::assertSame(0xAABBCCDD, $buffer->readDword());

        $buffer
            ->seek(-7, Origin::Current)
            ->writeByte(0x11)
            ->writeWord(0x7766)
            ->writeDword(0xFFEEDDAA)
            ->seek(-7, Origin::End);

        self::assertSame(0x11, $buffer->readByte());
        self::assertSame(0x7766, $buffer->readWord());
        self::assertSame(0xFFEEDDAA, $buffer->readDword());
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

        self::assertSame('CCBBAA', $buffer->readChars(6));

        $buffer
            ->truncate()
            ->writeDword(0x01020304)
            ->invert();

        self::assertSame(0x04030201, $buffer->readDword());
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
            self::assertSame(ord($str[$i]), $buffer[$i]);
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
            self::assertTrue(isset($buffer[$i]));
        }

        self::assertFalse(isset($buffer[$i]));

        $this->expectException(ByteBufferException::class);

        isset($buffer['a']);
    }

    public function testCastToString() : void
    {
        $buffer = (new ByteBuffer())
            ->setEndian(Endian::LittleEndian)
            ->writeChars('abcdef');

        self::assertSame("hex (6/6): 61626364 6566 - abcdef\n", (string) $buffer);

        $buffer
            ->truncate()
            ->writeChars('abcdefghijklmnopqrstuvwxyz0123456789');

        $output = <<<'TEST'
            hex (36/36):
            61626364 65666768 696a6b6c 6d6e6f70 71727374 - abcdefghijklmnopqrst
            75767778 797a3031 32333435 36373839          - uvwxyz0123456789

            TEST;

        self::assertSame($output, (string) $buffer);
    }

    public function testStringMethod() : void
    {
        $str = 'abcdef';

        $buffer = (new ByteBuffer())
            ->setEndian(Endian::LittleEndian)
            ->writeChars($str);

        self::assertSame($str, $buffer->string());
    }

    public function testCrc() : void
    {
        $str = 'abcdefghijklmnopqrstuvwxyz0123456789';

        $buffer = (new ByteBuffer())
            ->setEndian(Endian::LittleEndian)
            ->writeChars($str);

        self::assertSame(hash('crc32b', $str, false), $buffer->crc32b(true));
    }

    public function testHashes() : void
    {
        $str = 'abcdefghijklmnopqrstuvwxyz0123456789';

        $buffer = (new ByteBuffer())
            ->setEndian(Endian::LittleEndian)
            ->writeChars($str);

        self::assertSame(hash('sha1', $str, false), $buffer->sha1(false));
        self::assertSame(hash('sha256', $str, false), $buffer->sha256(false));
        self::assertSame(hash('md5', $str, false), $buffer->md5(false));

        self::assertSame(hash('sha1', $str, true), $buffer->sha1(true));
        self::assertSame(hash('sha256', $str, false), $buffer->sha256(false));
        self::assertSame(hash('md5', $str, true), $buffer->md5(true));
    }

    public function testDelete() : void
    {
        $str = 'abcdefghijklmnopqrstuvwxyz0123456789';

        $buffer = (new ByteBuffer())
            ->setEndian(Endian::LittleEndian)
            ->writeChars($str);

        $buffer->delete(10, 10);

        $buffer->seek(10, Origin::Start);

        self::assertSame('uvwxyz0123', $buffer->readChars(10));
    }

    public function testSub() : void
    {
        $str = 'abcdefghijklmnopqrstuvwxyz0123456789';

        $buffer = (new ByteBuffer())
            ->setEndian(Endian::LittleEndian)
            ->writeChars($str);

        $buffer = $buffer->copy(10, 10);

        self::assertSame('klmnopqrst', $buffer->readChars(10));
    }

    public function testUnset() : void
    {
        $this->expectException(ByteBufferException::class);
        $this->expectExceptionMessage('not implemented');

        $str = 'abcdefghijklmnopqrstuvwxyz0123456789';

        $buffer = (new ByteBuffer())
            ->setEndian(Endian::LittleEndian)
            ->writeChars($str);

        unset($buffer[4]);
    }
}
