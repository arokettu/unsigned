<?php

/** @noinspection DuplicatedCode */

declare(strict_types=1);

namespace Arokettu\Unsigned;

function from_int(int $value, int $sizeof): string
{
    $hex = \dechex($value);
    if (\strlen($hex) > $sizeof * 2) {
        throw new \InvalidArgumentException("$value does not fit into $sizeof bytes");
    }
    $hex = \str_pad($hex, $sizeof * 2, $value >= 0 ? '0' : 'f', STR_PAD_LEFT);

    if (\strlen($hex) % 2 === 1) {
        $hex = '0' . $hex;
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
    if (\strlen($value) !== $sizeof * 2) {
        $s = $sizeof * 2;
        throw new \InvalidArgumentException("Hex value for \$sizeof == $sizeof must be $s chars long");
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

    if ($shift < -1) {
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
    for ($i = 0; $i < $sizeof; ++$i) {
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

    if ($shift < -1) {
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
    for ($i = 0; $i < $sizeof; ++$i) {
        $newChr = \ord($value[$i]) << $shift | $carry;
        $value[$i] = \chr($newChr);
        $carry = \intdiv($newChr, 256);
    }

    $value[$i] = \chr($carry);

    return \substr($value, 1);
}

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
