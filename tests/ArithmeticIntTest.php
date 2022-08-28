<?php

namespace Arokettu\Unsigned\Tests;

use PHPUnit\Framework\TestCase;

use function Arokettu\Unsigned\add_int;
use function Arokettu\Unsigned\from_int;
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
}
