<?php

declare(strict_types=1);

namespace Arokettu\Unsigned\Tests;

use PHPUnit\Framework\TestCase;

use function Arokettu\Unsigned\from_hex;
use function Arokettu\Unsigned\from_int;
use function Arokettu\Unsigned\to_hex;
use function Arokettu\Unsigned\to_int;

class ImportExportTest extends TestCase
{
    public function testFromInt()
    {
        // target below PHP_INT_SIZE

        // positive
        self::assertEquals("\x23\x01", from_int(0x123, 2));
        // zero
        self::assertEquals("\0\0", from_int(0, 2));
        // negative
        self::assertEquals("\xf0\xff", from_int(-0x10, 2));

        // target PHP_INT_SIZE

        // positive
        self::assertEquals(\str_pad("\x23\x01", PHP_INT_SIZE, "\0"), from_int(0x123, PHP_INT_SIZE));
        // zero
        self::assertEquals(\str_repeat("\0", PHP_INT_SIZE), from_int(0, PHP_INT_SIZE));
        // negative
        self::assertEquals(\str_pad("\xf0\xff", PHP_INT_SIZE, "\xff"), from_int(-0x10, PHP_INT_SIZE));

        // target above PHP_INT_SIZE

        if (PHP_INT_SIZE > 8) {
            throw new \LogicException('The future arrived! Update tests!');
        }

        // positive
        self::assertEquals("\x78\x56\x34\x12\0\0\0\0\0\0\0\0\0\0\0\0", from_int(0x12345678, 16));
        // zero
        self::assertEquals("\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0", from_int(0, 16));
        // negative
        self::assertEquals("\0\0\0\xff\xff\xff\xff\xff\xff\xff\xff\xff\xff\xff\xff\xff", from_int(-0x1000000, 16));
    }

    public function testFromIntTruncate()
    {
        self::assertEquals(1193046 & 65535, to_int(from_int(1193046, 2)));
        self::assertEquals(-6636321 & 65535, to_int(from_int(-6636321, 2)));
    }

    public function testToInt()
    {
        self::assertEquals(0x123, to_int("\x23\x01"));
        self::assertEquals(PHP_INT_MAX, to_int(from_int(PHP_INT_MAX, PHP_INT_SIZE)));
        // negative of lower size than PHP_INT
        self::assertEquals(-1 & PHP_INT_MAX >> 7, to_int(from_int(-1, PHP_INT_SIZE - 1)));
    }

    public function testToIntTooBig()
    {
        $this->expectException(\RangeException::class);
        $this->expectExceptionMessage('The value is larger than PHP integer');

        to_int(str_repeat("\xff", PHP_INT_SIZE + 1));
    }

    public function testToIntTooBigNeg()
    {
        $this->expectException(\RangeException::class);
        $this->expectExceptionMessage('The value is larger than PHP integer');

        // negative of equal or greater size than PHP_INT
        to_int(from_int(-1, PHP_INT_SIZE));
    }

    public function testFromHex()
    {
        // exact
        self::assertEquals("\x23\x01", from_hex('0123', 2));
        // truncate
        self::assertEquals("\x23", from_hex('0123', 1));
        // pad
        self::assertEquals("\x23\x01\x00", from_hex('0123', 3));
        // odd number of digits is acceptable too!
        self::assertEquals("\x23\x01\x00\x00", from_hex('123', 4));
    }

    public function testToHex()
    {
        self::assertEquals('000123', to_hex(from_int(0x123, 3)));
    }
}
