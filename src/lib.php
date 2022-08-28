<?php

/**
 * @noinspection DuplicatedCode
 * @noinspection PhpFullyQualifiedNameUsageInspection
 */

declare(strict_types=1);

namespace Arokettu\Unsigned;

use Arokettu\Unsigned as u;

function from_int(int $value, int $sizeof): string
{
    $hex = \dechex($value);
    $strlen = \strlen($hex);
    $hexsize = $sizeof * 2;

    switch ($strlen <=> $hexsize) {
        case -1:
            // pad according to sign
            $hex = \str_pad($hex, $hexsize, $value >= 0 ? '0' : 'f', STR_PAD_LEFT);
            break;

        case 1:
            // truncate
            $hex = \substr($hex, -$hexsize);
            break;

        // @codeCoverageIgnoreStart
        // coverage bug?
        default:
            // nothing
        // @codeCoverageIgnoreEnd
    }

    return \strrev(\hex2bin($hex));
}

function to_int(string $value): int
{
    $value = \rtrim($value, "\0");

    if (
        \strlen($value) > PHP_INT_SIZE ||
        \strlen($value) === PHP_INT_SIZE && \ord($value[PHP_INT_SIZE - 1]) > 127
    ) {
        throw new \RangeException('The value is larger than PHP integer');
    }

    return \hexdec(\bin2hex(\strrev($value)));
}

function from_hex(string $value, int $sizeof): string
{
    $strlen = \strlen($value);
    $hexsize = $sizeof * 2;

    switch ($strlen <=> $hexsize) {
        case -1:
            // pad with zeros
            $value = \str_pad($value, $hexsize, '0', STR_PAD_LEFT);
            break;

        case 1:
            // truncate
            $value = \substr($value, -$hexsize);
            break;

        // @codeCoverageIgnoreStart
        // coverage bug?
        default:
            // nothing
        // @codeCoverageIgnoreEnd
    }

    return \strrev(\hex2bin($value));
}

function to_hex(string $value): string
{
    return \bin2hex(\strrev($value));
}

/**
 * $value << $shift
 */
function shift_left(string $value, int $shift): string
{
    $sizeof = \strlen($value);

    if ($shift < 0) {
        throw new \InvalidArgumentException('$shift must be non negative');
    }
    if ($shift >= $sizeof * 8) {
        return \str_repeat("\0", $sizeof);
    }
    if ($shift >= 8) {
        $easyShift = \intdiv($shift, 8);
        $shift = $shift % 8;
        $value = \str_repeat("\0", $easyShift) . \substr($value, 0, $sizeof - $easyShift);
    }
    if ($shift === 0) {
        return $value;
    }

    $carry = 0;
    for ($i = 0; $i < $sizeof; $i++) {
        $newChr = \ord($value[$i]) << $shift | $carry;
        $value[$i] = \chr($newChr);
        $carry = \intdiv($newChr, 256);
    }

    return $value;
}

/**
 * $value >> $shift
 */
function shift_right(string $value, int $shift): string
{
    $sizeof = \strlen($value);

    if ($shift < 0) {
        throw new \InvalidArgumentException('$shift must be non negative');
    }
    if ($shift >= $sizeof * 8) {
        return \str_repeat("\0", $sizeof);
    }
    if ($shift >= 8) {
        $easyShift = \intdiv($shift, 8);
        $shift = $shift % 8;
        $value = \substr($value, $easyShift) . \str_repeat("\0", $easyShift);
    }
    if ($shift === 0) {
        return $value;
    }

    // shift left 8 - $shift bits, then remove the least significant byte
    $carry = 0;
    $shift = 8 - $shift;
    for ($i = 0; $i < $sizeof; $i++) {
        $newChr = \ord($value[$i]) << $shift | $carry;
        $value[$i] = \chr($newChr);
        $carry = \intdiv($newChr, 256);
    }

    $value[$i] = \chr($carry);

    return \substr($value, 1);
}

/**
 * a + b
 */
function add(string $a, string $b): string
{
    $sizeof = \strlen($a);
    $sizeofb = \strlen($b);
    if ($sizeof !== $sizeofb) {
        throw new \InvalidArgumentException("Arguments must be the same size, $sizeof and $sizeofb bytes given");
    }

    $carry = 0;
    for ($i = 0; $i < $sizeof; ++$i) {
        $newChr = \ord($a[$i]) + \ord($b[$i]) + $carry;
        $a[$i] = \chr($newChr);
        $carry = \intdiv($newChr, 256);
    }

    return $a;
}

/**
 * a + int(b)
 */
function add_int(string $a, int $b): string
{
    $sizeof = \strlen($a);

    if ($b === 0) {
        return $a;
    }
    if ($b < 0) {
        // fall back to slower algorithm
        return u\add($a, u\from_int($b, $sizeof));
    }

    $carry = $b;
    for ($i = 0; $i < $sizeof; ++$i) {
        if ($carry === 0) {
            break;
        }
        $newChr = \ord($a[$i]) + $carry;
        if (\is_float($newChr)) {
            // overflow, fall back to slower algorithm
            return u\add($a, u\from_int($b, $sizeof));
        }
        $a[$i] = \chr($newChr);
        $carry = \intdiv($newChr, 256);
    }

    return $a;
}

/**
 * a - b
 */
function sub(string $a, string $b): string
{
    return u\add($a, u\neg($b));
}

/**
 * a - int(b)
 */
function sub_int(string $a, int $b): string
{
    // handle overflow on negative
    if ($b === PHP_INT_MIN) {
        return u\sub($a, u\from_int(PHP_INT_MIN, \strlen($a)));
    }
    return u\add_int($a, -$b); // mostly syntax sugar, will likely go for a slow algo
}

/**
 * int(a) - b
 */
function sub_int_rev(int $a, string $b): string
{
    return u\add_int(u\neg($b), $a);
}

/**
 * -a
 */
function neg(string $a): string
{
    return u\add_int(~$a, 1);
}

/**
 * a * b
 */
function mul(string $a, string $b): string
{
    $sizeof = \strlen($a);
    $sizeofb = \strlen($b);
    if ($sizeof !== $sizeofb) {
        throw new \InvalidArgumentException("Arguments must be the same size, $sizeof and $sizeofb bytes given");
    }

    $newval = \str_repeat("\0", $sizeof);

    for ($i = 0; $i < $sizeof; $i++) {
        $carry = 0;
        $ord = \ord($a[$i]);
        for ($j = 0; $j < $sizeof - $i; $j++) {
            $idx = $i + $j;

            $newChr = $ord * \ord($b[$j]) + \ord($newval[$idx]) + $carry;
            $newval[$idx] = \chr($newChr);
            $carry = \intdiv($newChr, 256);
        }
    }

    return $newval;
}
