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

enum Origin
{
    case None;
    case Start;
    case Current;
    case End;
}

class ByteBufferException extends Exception
{
}

class ByteBuffer implements ArrayAccess
{
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

        $output = "hex ({$length}):";

        if ($length > 20) {
            $output .= "\n";
        }

        $hex = '';
        $ascii = '';

        for ($i = 0; $i < $length; ++$i) {
            if ($i && !($i % 4)) {
                $hex .= ' ';
            }

            if ($i && !($i % 20)) {
                $output .= $hex . '- ' . $ascii . "\n";

                $hex = '';
                $ascii = '';
            }

            $hex .= sprintf('%02x', ord($this->data[$i]));

            $data = ord($this->data[$i]);

            $ascii .= ($data >= 0x20 && $data < 0x7F) ? $this->data[$i] : '.';
        }

        if (strlen($hex)) {
            if ($length > 20) {
                $output .= str_pad($hex, 44) . ' - ' . $ascii . "\n";
            } else {
                $output .= ' '. $hex . ' - ' . $ascii . "\n";
            }
        }

        return $output;
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

    public function seek(int $offset, Origin $origin) : self
    {
        switch ($origin) {
            case Origin::Start:
                $position = $offset;
                break;

            case Origin::Current:
                $position = $this->position + $offset;
                break;

            case Origin::End:
                $position = $this->length() + $offset;
                break;

            // @codeCoverageIgnoreStart
            default:
                throw new ByteBufferException('origin not set');
            // @codeCoverageIgnoreEnd
        }

        if ($position < 0 || $position > $this->length()) {
            throw new ByteBufferException('position out of range');
        }

        $this->position = $position;

        return $this;
    }

    public function read(int $length) : string
    {
        if ($this->position + $length < 0 || $this->position + $length > $this->length()) {
            throw new ByteBufferException('out of range');
        }

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
        if ($this->endian === Endian::None) {
                throw new ByteBufferException('endian not set');
        }

        return unpack($this->endian === Endian::LittleEndian ? 'v' : 'n', $this->read(2))[1];
    }

    public function readDword() : int
    {
        if ($this->endian === Endian::None) {
            throw new ByteBufferException('endian not set');
        }

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
        throw new ByteBufferException('unhandled exception');
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
        $data = chr($data & 0x000000FF);
        //$this->data .= pack('C', $data);

        if ($this->position === $this->length()) {
            $this->data .= $data;
        } else {
            $this->data = substr_replace($this->data, $data, $this->position, 1);
        }

        ++$this->position;

        return $this;
    }

    public function writeWord(int $data) : self
    {
        if ($this->endian === Endian::None) {
            throw new ByteBufferException('endian not set');
        }

        $data = pack($this->endian === Endian::LittleEndian ? 'v' : 'n', $data);
        //$this->data .= chr(($data & 0x0000ff00) >> 8);
        //$this->data .= chr($data & 0x000000ff);

        if ($this->position === $this->length()) {
            $this->data .= $data;
        } else {
            $this->data = substr_replace($this->data, $data, $this->position, 2);
        }

        $this->position += 2;

        return $this;
    }

    public function writeDword(int $data) : self
    {
        if ($this->endian === Endian::None) {
            throw new ByteBufferException('endian not set');
        }

        $data = pack($this->endian === Endian::LittleEndian ? 'V' : 'N', $data);
        //$this->data .= chr(($data & 0xff000000) >> 24);
        //$this->data .= chr(($data & 0x00ff0000) >> 16);
        //$this->data .= chr(($data & 0x0000ff00) >> 8);
        //$this->data .= chr($data & 0x000000ff);

        if ($this->position === $this->length()) {
            $this->data .= $data;
        } else {
            $this->data = substr_replace($this->data, $data, $this->position, 4);
        }

        $this->position += 4;

        return $this;
    }

    public function writeChars(string $data) : self
    {
        if ($this->position === $this->length()) {
            $this->data .= $data;
        } else {
            $this->data = substr_replace($this->data, $data, $this->position, strlen($data));
        }

        $this->position += strlen($data);

        return $this;
    }

    public function writeString(string $data) : self
    {
        $data .= chr(0x0);

        if ($this->position === $this->length()) {
            $this->data .= $data;
        } else {
            $this->data = substr_replace($this->data, $data, $this->position, strlen($data));
        }

        $this->position += strlen($data);

        return $this;
    }

    public function insertByte(int $data) : self
    {
        $this->data = substr_replace($this->data, str_pad('', 1), $this->position, 0);
        return $this->writeByte($data);
    }

    public function insertWord(int $data) : self
    {
        $this->data = substr_replace($this->data, str_pad('', 2), $this->position, 0);
        return $this->writeWord($data);
    }

    public function insertDword(int $data) : self
    {
        $this->data = substr_replace($this->data, str_pad('', 4), $this->position, 0);
        return $this->writeDword($data);
    }

    public function insertChars(string $data) : self
    {
        $this->data = substr_replace($this->data, str_pad('', strlen($data)), $this->position, 0);
        return $this->writeChars($data);
    }

    public function insertString(string $data) : self
    {
        $this->data = substr_replace($this->data, str_pad('', strlen($data) + 1), $this->position, 0);
        return $this->writeString($data);
    }

    public function delete(int $position, int $length) : self
    {
        $this->data = substr_replace($this->data, '', $position, $length);
        return $this;
    }

    public function invert() : self
    {
        $this->seek(0, Origin::Start);

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
            throw new ByteBufferException('offset must be integer');
        }

        if ($offset >= $this->length() || $offset < 0) {
            throw new ByteBufferException('out of range');
        }

        return ord($this->data[$offset]);
    }

    public function offsetSet(mixed $offset, mixed $value) : void
    {
        if (gettype($offset) !== 'integer') {
            throw new ByteBufferException('offset must be integer');
        }

        if ($offset >= $this->length() || $offset < 0) {
            throw new ByteBufferException('out of range');
        }

        $this->data[$offset] = $value;
    }

    public function offsetExists(mixed $offset) : bool
    {
        if (gettype($offset) !== 'integer') {
            throw new ByteBufferException('offset must be integer');
        }

        if ($offset >= 0 && $offset < $this->length()) {
            return true;
        }

        return false;
    }

    public function offsetUnset(mixed $offset) : void
    {
        throw new ByteBufferException('not implemented');
        //unset($this->data[$offset]);
    }

    public function crc32b(bool $asString = false) : int|string
    {
        $crc = crc32($this->data);

        return $asString ? dechex($crc) : $crc;
    }
}
