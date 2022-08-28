<?php

declare(strict_types=1);

namespace Arokettu\Unsigned\Tests;

use PHPUnit\Framework\TestCase;

use function Arokettu\Unsigned\add_int;
use function Arokettu\Unsigned\from_int;
use function Arokettu\Unsigned\sub_int;
use function Arokettu\Unsigned\sub_int_rev;
use function Arokettu\Unsigned\to_int;

class ArithmeticIntTest extends TestCase
{
    public function testSum()
    {
        // normal
        self::assertEquals(
            123456 + 654321,
            to_int(add_int(from_int(123456, PHP_INT_SIZE), 654321))
        );
        //overflow
        self::assertEquals(
            (123 + 234) & 255,
            to_int(add_int(from_int(123, 1), 234))
        );
        // zero
        self::assertEquals(
            123456,
            to_int(add_int(from_int(123456, PHP_INT_SIZE), 0))
        );
        // negative
        self::assertEquals(
            123456 - 456,
            to_int(add_int(from_int(123456, PHP_INT_SIZE), -456))
        );
        // int overflow
        self::assertEquals(
            from_int(-2, PHP_INT_SIZE),
            add_int(from_int(PHP_INT_MAX, PHP_INT_SIZE), PHP_INT_MAX)
        );
    }

    public function testSub()
    {
        // normal
        self::assertEquals(
            654321 - 123456,
            to_int(sub_int(from_int(654321, PHP_INT_SIZE), 123456))
        );
        // overflow
        self::assertEquals(
            (123456 - 654321) & PHP_INT_MAX >> 7,
            to_int(sub_int(from_int(123456, PHP_INT_SIZE - 1), 654321))
        );
        // special
        self::assertEquals(
            '40e2010000000080', // larger than int
            bin2hex(sub_int(from_int(123456, PHP_INT_SIZE), PHP_INT_MIN))
        );
    }

    public function testSubRev()
    {
        // normal
        self::assertEquals(
            654321 - 123456,
            to_int(sub_int_rev(654321, from_int(123456, PHP_INT_SIZE)))
        );
        // overflow
        self::assertEquals(
            (123456 - 654321) & PHP_INT_MAX >> 7,
            to_int(sub_int_rev(123456, from_int(654321, PHP_INT_SIZE - 1)))
        );
        // not special but check anyway
        self::assertEquals(
            PHP_INT_MAX - 123455, // overflow
            to_int(sub_int_rev(PHP_INT_MIN, from_int(123456, PHP_INT_SIZE)))
        );
    }
}
