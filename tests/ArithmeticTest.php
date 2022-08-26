<?php

declare(strict_types=1);

namespace Arokettu\Unsigned\Tests;

use PHPUnit\Framework\TestCase;

use function Arokettu\Unsigned\add;
use function Arokettu\Unsigned\from_int;
use function Arokettu\Unsigned\mul;
use function Arokettu\Unsigned\sub;
use function Arokettu\Unsigned\to_int;

class ArithmeticTest extends TestCase
{
    public function testSum()
    {
        // normal
        self::assertEquals(
            123456 + 654321,
            to_int(add(from_int(123456, PHP_INT_SIZE), from_int(654321, PHP_INT_SIZE)))
        );
        //overflow
        self::assertEquals(
            (123 + 234) & 255,
            to_int(add(from_int(123, 1), from_int(234, 1)))
        );
    }

    public function testSumDifferentSizes()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Arguments must be the same size, 1 and 2 bytes given');

        add("\0", "\0\0");
    }

    public function testSub()
    {
        // normal
        self::assertEquals(
            654321 - 123456,
            to_int(sub(from_int(654321, PHP_INT_SIZE), from_int(123456, PHP_INT_SIZE)))
        );
        // overflow
        self::assertEquals(
            (123456 - 654321) & PHP_INT_MAX >> 7,
            to_int(sub(from_int(123456, PHP_INT_SIZE - 1), from_int(654321, PHP_INT_SIZE - 1)))
        );
    }

    public function testSubDifferentSizes()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Arguments must be the same size, 1 and 2 bytes given');

        sub("\0", "\0\0");
    }

    public function testMul()
    {
        // normal
        self::assertEquals(
            11111 * 11111,
            to_int(mul(from_int(11111, PHP_INT_SIZE), from_int(11111, PHP_INT_SIZE)))
        );
        //overflow
        self::assertEquals(
            (11111 * 11111) & 65535,
            to_int(mul(from_int(11111, 2), from_int(11111, 2)))
        );
    }

    public function testMulDifferentSizes()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Arguments must be the same size, 1 and 2 bytes given');

        mul("\0", "\0\0");
    }
}
