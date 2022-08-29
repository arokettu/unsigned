<?php

declare(strict_types=1);

namespace Arokettu\Unsigned\Tests;

use PHPUnit\Framework\TestCase;

use function Arokettu\Unsigned\add;
use function Arokettu\Unsigned\div;
use function Arokettu\Unsigned\div_mod;
use function Arokettu\Unsigned\from_int;
use function Arokettu\Unsigned\mod;
use function Arokettu\Unsigned\mul;
use function Arokettu\Unsigned\neg;
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

    public function testNeg()
    {
        // something that converts to int
        self::assertEquals(
            123,
            to_int(neg(from_int(-123, PHP_INT_SIZE)))
        );
        // 0
        self::assertEquals(
            0,
            to_int(neg(from_int(0, PHP_INT_SIZE)))
        );
        // small size
        self::assertEquals(
            255,
            to_int(neg(from_int(1, 1)))
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
        // 0
        self::assertEquals(
            0,
            to_int(mul(from_int(11111, 2), from_int(0, 2)))
        );
        // 1
        self::assertEquals(
            11111,
            to_int(mul(from_int(11111, 2), from_int(1, 2)))
        );
        // -1
        self::assertEquals(
            from_int(-11111, 2),
            mul(from_int(11111, 2), from_int(-1, 2))
        );
    }

    public function testMulDifferentSizes()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Arguments must be the same size, 1 and 2 bytes given');

        mul("\0", "\0\0");
    }

    public function testDiv()
    {
//        self::assertEquals(123456 % 1000, to_int(mod(from_int(123456, 8), from_int(1000, 8)))));
        self::assertEquals(\intdiv(123456, 1), to_int(div(from_int(123456, 8), from_int(1, 8))));
        self::assertEquals(\intdiv(123456, 1024), to_int(div(from_int(123456, 8), from_int(1024, 8))));
        self::assertEquals(\intdiv(123456, 654321), to_int(div(from_int(123456, 8), from_int(654321, 8))));
        self::assertEquals(\intdiv(123456, 123456), to_int(div(from_int(123456, 8), from_int(123456, 8))));
        // negative is accepted
        // self::assertEquals(...?, to_int(mod(from_int(123456, 8), from_int(-1000, 8)))));
    }

    public function testDivDifferentSizes()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Arguments must be the same size, 1 and 2 bytes given');

        div("\0", "\0\0");
    }

    public function testDivNoZero()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Division by zero');

        div(from_int(123456, 8), from_int(0, 8));
    }

    public function testMod()
    {
//        self::assertEquals(123456 % 1000, to_int(mod(from_int(123456, 8), from_int(1000, 8)))));
        self::assertEquals(123456 % 1, to_int(mod(from_int(123456, 8), from_int(1, 8))));
        self::assertEquals(123456 % 1024, to_int(mod(from_int(123456, 8), from_int(1024, 8))));
        self::assertEquals(123456 % 654321, to_int(mod(from_int(123456, 8), from_int(654321, 8))));
        self::assertEquals(123456 % 123456, to_int(mod(from_int(123456, 8), from_int(123456, 8))));
        // negative is accepted
        // self::assertEquals(...?, to_int(mod(from_int(123456, 8), from_int(-1000, 8)))));
    }

    public function testModNoZero()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Modulo by zero');

        mod(from_int(123456, 8), from_int(0, 8));
    }

    public function testModDifferentSizes()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Arguments must be the same size, 1 and 2 bytes given');

        mod("\0", "\0\0");
    }

    public function testDivMod()
    {
//        self::assertEquals(123456 % 1000, to_int(mod(from_int(123456, 8), from_int(1000, 8)))));
        self::assertEquals(123456 % 1, to_int(div_mod(from_int(123456, 8), from_int(1, 8))[1]));
        self::assertEquals(123456 % 1024, to_int(div_mod(from_int(123456, 8), from_int(1024, 8))[1]));
        self::assertEquals(123456 % 654321, to_int(div_mod(from_int(123456, 8), from_int(654321, 8))[1]));
        self::assertEquals(123456 % 123456, to_int(div_mod(from_int(123456, 8), from_int(123456, 8))[1]));
        // negative is accepted
        // self::assertEquals(...?, to_int(mod(from_int(123456, 8), from_int(-1000, 8)))));
    }
}
