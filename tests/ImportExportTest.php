<?php

declare(strict_types=1);

namespace Arokettu\Unsigned\Tests;

use PHPUnit\Framework\TestCase;

use function Arokettu\Unsigned\from_int;

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

    public function testFromIntOverflow()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('1193046 does not fit into 2 bytes');

        from_int(1193046, 2);
    }

    public function testFromIntOverflowNeg()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('-6636321 does not fit into 2 bytes');

        from_int(-6636321, 2);
    }
}
