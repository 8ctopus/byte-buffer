<?php

namespace Oct8pus\ByteBuffer;

use ArrayAccess;
use Exception;

enum Endian
{
    case None;

    case LittleEndian;

    case BigEndian;
}

class BufferException extends Exception
{

}

class ByteBuffer implements ArrayAccess
{
    private const INT_SIZE = 4;

    private Endian $endian;

    private string $data;
    private int $position;

    public function __construct()
    {
        $this->endian = Endian::None;
        $this->data = '';
        $this->position = 0;
    }

    public function __toString() : string
    {
        $length = $this->length();

        $hex = "hex ({$length}):";
        $ascii = '';

        for ($i = 0; $i < $length; ++$i) {
            if (!($i % 4)) {
                $hex .= ' ';
            }

            if (!($i % 20)) {
                $hex .= $ascii . "\n";
                $ascii = '';
            }

            $hex .= sprintf('%02x', ord($this->data[$i]));

            $data = ord($this->data[$i]);

            $ascii .= ord($this->data[$i]) >= 0x20 ? $this->data[$i] : '.';
        }

        return $hex . ' - ' . $ascii . "\n";
    }

    public function endian() : Endian
    {
        return $this->endian;
    }

    public function setEndian(Endian $endian) : self
    {
        $this->endian = $endian;
        return $this;
    }

    public function string() : string
    {
        return $this->data;
    }

    public function length() : int
    {
        return strlen($this->data);
    }

    public function position() : int
    {
        return $this->position;
    }

    public function setPosition($position) : self
    {
        assert($position >= 0 && $position < strlen($this->data));

        $this->position = $position;

        return $this;
    }

    public function read(int $length) : string
    {
        assert($this->length() >= $this->position + $length);

        $data = substr($this->data, $this->position, $length);

        $this->position += $length;

        return $data;
    }

    public function readByte() : int
    {
        $data = $this->read(1);

        return ord($data);
    }

    public function readWord() : int
    {
        assert($this->endian !== Endian::None);

        return unpack($this->endian === Endian::LittleEndian ? 'v' : 'n', $this->read(2))[1];
    }

    public function readDword() : int
    {
        assert($this->endian !== Endian::None);

        return unpack($this->endian === Endian::LittleEndian ? 'V' : 'N', $this->read(4))[1];
    }

    public function readChars(int $length) : string
    {
        return $this->read($length);
    }

    public function readString() : string
    {
        $str = '';

        while (true) {
            $data = $this->read(1);

            if (ord($data) === 0x0) {
                return $str;
            }

            $str .= $data;
        }

        // @codeCoverageIgnoreStart
        throw new BufferException('unhandled exception');
        // @codeCoverageIgnoreEnd
    }

    public function truncate() : self
    {
        $this->data = '';
        $this->position = 0;

        return $this;
    }

    public function writeByte(int $data) : self
    {
        $this->data .= chr($data & 0x000000FF);
        //$this->data .= pack('C', $data);

        ++$this->position;

        return $this;
    }

    public function writeWord(int $data) : self
    {
        assert($this->endian !== Endian::None);

        $this->data .= pack($this->endian === Endian::LittleEndian ? 'v' : 'n', $data);

        //$this->data .= chr(($data & 0x0000ff00) >> 8);
        //$this->data .= chr($data & 0x000000ff);

        $this->position += 2;

        return $this;
    }

    public function writeDword(int $data) : self
    {
        assert($this->endian !== Endian::None);

        $this->data .= pack($this->endian === Endian::LittleEndian ? 'V' : 'N', $data);

        //$this->data .= chr(($data & 0xff000000) >> 24);
        //$this->data .= chr(($data & 0x00ff0000) >> 16);
        //$this->data .= chr(($data & 0x0000ff00) >> 8);
        //$this->data .= chr($data & 0x000000ff);

        $this->position += 4;

        return $this;
    }

    public function writeChars(string $data) : self
    {
        $this->data .= $data;

        $this->position += strlen($data);

        return $this;
    }

    public function writeString(string $data) : self
    {
        $this->data .= $data;
        $this->data .= chr(0x0);

        $this->position += strlen($data) + 1;

        return $this;
    }

    public function invert() : self
    {
        $this->setPosition(0);

        $length = $this->length();

        for ($i = 0; $i < floor($length / 2); ++$i) {
            $data = $this->data[$length - 1 - $i];
            $this->data[$length - 1 - $i] = $this->data[$i];
            $this->data[$i] = $data;
        }

        return $this;
    }

    public function offsetGet(mixed $offset) : int
    {
        if (gettype($offset) !== 'integer') {
            throw new BufferException('offset must be integer');
        }

        if ($offset >= $this->length() || $offset < 0) {
            throw new BufferException('out of range');
        }

        return ord($this->data[$offset]);
    }

    public function offsetSet(mixed $offset, mixed $value) : void
    {
        if (gettype($offset) !== 'integer') {
            throw new BufferException('offset must be integer');
        }

        if ($offset >= $this->length() || $offset < 0) {
            throw new BufferException('out of range');
        }

        $this->data[$offset] = $value;
    }

    public function offsetExists(mixed $offset) : bool
    {
        if (gettype($offset) !== 'integer') {
            throw new BufferException('offset must be integer');
        }

        if ($offset >= 0 && $offset < $this->length()) {
            return true;
        }

        return false;
    }

    public function offsetUnset(mixed $offset) : void
    {
        throw new BufferException();
        //unset($this->data[$offset]);
    }

    public function crc32b(bool $asString = false) : int|string
    {
        $crc = crc32($this->data);

        return $asString ? dechex($crc) : $crc;
    }
}
