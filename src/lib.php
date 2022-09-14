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
    }

    return \strrev(\hex2bin($hex));
}

function to_int(string $value): int
{
    if (!u\fits_into_int($value)) {
        throw new \RangeException('The value is larger than PHP integer');
    }

    return \hexdec(\bin2hex(\strrev($value)));
}

function to_signed_int(string $value): int
{
    $sizeof = \strlen($value);
    if (u\is_bit_set($value, $sizeof * 8 - 1)) {
        $value = ~$value;
        return -u\to_int($value) - 1;
    }

    return u\to_int($value);
}

function fits_into_int(string $value): bool
{
    $value = \rtrim($value, "\0");
    $sizeof = \strlen($value);

    $notFits =
        $sizeof > PHP_INT_SIZE ||
        $sizeof === PHP_INT_SIZE && \ord($value[PHP_INT_SIZE - 1]) > 127;

    return !$notFits;
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
        $easyShift = $shift >> 3; // div 8
        $shift = $shift & 7; // mod 8
        $value = \str_repeat("\0", $easyShift) . \substr($value, 0, $sizeof - $easyShift);
    }
    if ($shift === 0) {
        return $value;
    }

    $carry = 0;
    for ($i = 0; $i < $sizeof; $i++) {
        $newChr = \ord($value[$i]) << $shift | $carry;
        $value[$i] = \chr($newChr);
        $carry = $newChr >> 8;
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
        $easyShift = $shift >> 3; // div 8
        $shift = $shift & 7; // mod 8
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
        $carry = $newChr >> 8;
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
        $carry = $newChr >> 8;
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
        $carry = $newChr >> 8;
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
    return u\add_int($a, -$b);
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
function mul(string $a, string $b, bool $forceSlow = false): string
{
    $sizeof = \strlen($a);
    $sizeofb = \strlen($b);
    if ($sizeof !== $sizeofb) {
        throw new \InvalidArgumentException("Arguments must be the same size, $sizeof and $sizeofb bytes given");
    }
    // if we're lucky to have a small $a
    if (!$forceSlow && u\fits_into_int($a)) {
        return u\mul_int($b, u\to_int($a));
    }
    // or $b
    if (!$forceSlow && u\fits_into_int($b)) {
        return u\mul_int($a, u\to_int($b));
    }

    $newval = \str_repeat("\0", $sizeof);

    for ($i = 0; $i < $sizeof; $i++) {
        $carry = 0;
        $ord = \ord($a[$i]);
        for ($j = 0; $j < $sizeof - $i; $j++) {
            $idx = $i + $j;

            $newChr = $ord * \ord($b[$j]) + \ord($newval[$idx]) + $carry;
            $newval[$idx] = \chr($newChr);
            $carry = $newChr >> 8;
        }
    }

    return $newval;
}

/**
 * a * int(b)
 */
function mul_int(string $a, int $b): string
{
    $sizeof = \strlen($a);

    // special cases
    if ($b === 0) {
        return \str_repeat("\0", $sizeof);
    }
    if ($b === 1) {
        return $a;
    }
    if ($b === -1) {
        return u\neg($a);
    }
    // overflow for the next handler
    if ($b === PHP_INT_MIN) {
        return u\mul($a, u\from_int(PHP_INT_MIN, $sizeof));
    }
    // we handle only positive, but we can move the 'sign' to the left
    if ($b < 0) {
        return u\mul_int(u\neg($a), -$b);
    }

    $carry = 0;
    for ($i = 0; $i < $sizeof; ++$i) {
        $newChr = \ord($a[$i]) * $b + $carry;
        if (\is_float($newChr)) {
            // overflow, fall back to slower algorithm
            return u\mul($a, u\from_int($b, $sizeof), true);
        }
        $a[$i] = \chr($newChr);
        $carry = $newChr >> 8;
    }

    return $a;
}

/**
 * a / b, a % b
 *
 * @return array [div -> string, mod -> string]
 */
function div_mod(string $a, string $b, bool $forceSlow = false): array
{
    $sizeof = \strlen($a);
    $sizeofb = \strlen($b);
    if ($sizeof !== $sizeofb) {
        throw new \InvalidArgumentException("Arguments must be the same size, $sizeof and $sizeofb bytes given");
    }

    // special cases
    $zero = \str_repeat("\0", $sizeof);
    $compare = u\compare($a, $b);
    // if a < b, result is 0 and modulo is a
    if ($compare < 0) {
        return [$zero, $a];
    }
    $one = $zero;
    $one[0] = "\1";
    // if a = b, result is 1 and modulo is 0
    if ($compare === 0) {
        return [$one, $zero];
    }
    // 0
    if ($b === $zero) {
        throw new \InvalidArgumentException('Division by zero');
    }
    // 1
    if ($b === $one) {
        return [$a, $zero];
    }
    // for pow2 just cut the required bits
    $b1 = u\add_int($b, -1);
    if (($b & $b1) === $zero) {
        $i = 0;
        while (!u\is_bit_set($b, $i)) {
            $i++;
        }
        return [
            u\shift_right($a, $i),
            $a & $b1,
        ];
    }
    // if we're lucky to have a small $b
    if (!$forceSlow && u\fits_into_int($b)) {
        list($div, $mod) = u\div_mod_int($a, u\to_int($b));
        return [
            $div,
            u\from_int($mod, $sizeof),
        ];
    }

    $b = \rtrim($b, "\0"); // only significant bytes
    $bZero = $b . "\0";
    $bAdd = u\neg($bZero);
    $sizeofFrame = \strlen($b);

    $r = $zero;
    $m = $sizeofFrame === $sizeof ? $zero : \str_repeat("\0", $sizeofFrame);

    $i = $sizeof;
    while ($i--) {
        $m = $a[$i] . $m;
        $chr = 0;
        while (u\compare($m, $bZero) > 0) {
            $m = u\add($m, $bAdd);
            $chr++;
        }
        $r[$i] = \chr($chr);
        $m = \substr($m, 0, $sizeofFrame);
    }

    return [$r, \str_pad($m, $sizeof, "\0")];
}

/**
 * a / int(b), a % int(b)
 *
 * @return array [div -> string, mod -> int]
 */
function div_mod_int(string $a, int $b): array
{
    $sizeof = \strlen($a);

    // special cases
    if ($b === 0) {
        throw new \InvalidArgumentException('Division by zero');
    }
    if ($b === 1) {
        return [$a, 0];
    }
    // for pow2 just cut the required bits
    if (($b & ($b - 1)) === 0) {
        $i = 0;
        while (1 << $i !== $b) {
            $i++;
        }
        return [
            u\shift_right($a, $i),
            u\to_int($a & u\from_int($b - 1, $sizeof)),
        ];
    }
    // can't handle negative, convert to unsigned first
    if ($b < 0) {
        throw new \InvalidArgumentException(
            '$b must be greater than zero. Use div_mod($a, from_int($b)) for unsigned logic'
        );
    }

    $mod = 0;
    $div = \str_repeat("\0", $sizeof);
    $i = $sizeof;
    while ($i--) {
        $dividend = $mod << 8 | \ord($a[$i]);
        if ($dividend < 0 || !\is_int($dividend)) {
            $divmod = u\div_mod($a, u\from_int($b, $sizeof), true);
            return [$divmod[0], u\to_int($divmod[1])];
        }
        $div[$i] = \chr(\intdiv($dividend, $b));
        $mod = $dividend % $b;
    }

    return [$div, $mod];
}

/**
 * a / b
 */
function div(string $a, string $b): string
{
    return u\div_mod($a, $b)[0];
}

/**
 * a / int(b)
 */
function div_int(string $a, int $b): string
{
    // can't handle negative, convert to unsigned first
    // custom message
    if ($b < 0) {
        throw new \InvalidArgumentException(
            '$b must be greater than zero. Use div($a, from_int($b)) for unsigned logic'
        );
    }

    return u\div_mod_int($a, $b)[0];
}

/**
 * a % b
 */
function mod(string $a, string $b): string
{
    $sizeof = \strlen($a);
    $sizeofb = \strlen($b);
    if ($sizeof !== $sizeofb) {
        throw new \InvalidArgumentException("Arguments must be the same size, $sizeof and $sizeofb bytes given");
    }

    // special cases
    $compare = u\compare($a, $b);
    // if a < b, entire a is modulo
    if ($compare < 0) {
        return $a;
    }
    $zero = \str_repeat("\0", $sizeof);
    // if a = b, modulo is 0
    if ($compare === 0) {
        return $zero;
    }
    // 0
    if ($b === $zero) {
        throw new \InvalidArgumentException('Modulo by zero');
    }
    // 1
    $one = $zero;
    $one[0] = "\1";
    if ($b === $one) {
        return $zero;
    }
    // for pow2 just cut the required bits
    $b1 = u\add_int($b, -1);
    if (($b & $b1) === $zero) {
        return $a & $b1;
    }
    // if we're lucky to have a small $b
    if (u\fits_into_int($b)) {
        return u\from_int(u\mod_int($a, u\to_int($b)), $sizeof);
    }

    // do a slow algo
    return u\div_mod($a, $b)[1];
}

/**
 * a % int(b) -> int
 */
function mod_int(string $a, int $b): int
{
    $sizeof = \strlen($a);

    // special cases
    if ($b === 0) {
        throw new \InvalidArgumentException('Modulo by zero');
    }
    if ($b === 1) {
        return 0;
    }
    // for pow2 just cut the required bits
    if (($b & ($b - 1)) === 0) {
        return u\to_int($a & u\from_int($b - 1, $sizeof));
    }
    // can't handle negative, convert to unsigned first
    if ($b < 0) {
        throw new \InvalidArgumentException(
            '$b must be greater than zero. Use mod($a, from_int($b)) for unsigned logic'
        );
    }

    $mod = 0;
    $i = $sizeof;
    while ($i--) {
        $dividend = $mod << 8 | \ord($a[$i]);
        if ($dividend < 0 || !\is_int($dividend)) {
            // overflow
            return u\to_int(u\div_mod($a, u\from_int($b, $sizeof), true)[1]);
        }
        $mod = $dividend % $b;
    }

    return $mod;
}

/**
 * a <=> b
 */
function compare(string $a, string $b): int
{
    $sizeof = \strlen($a);
    $sizeofb = \strlen($b);
    if ($sizeof !== $sizeofb) {
        throw new \InvalidArgumentException("Arguments must be the same size, $sizeof and $sizeofb bytes given");
    }

    $i = $sizeof;
    while ($i--) {
        $compare = $a[$i] <=> $b[$i];
        if ($compare !== 0) {
            return $compare;
        }
    }
    return 0;
}

function set_bit(string $a, int $bit): string
{
    $sizeof = \strlen($a);
    if ($bit < 0) {
        throw new \UnderflowException("Bit must be in range 0-" . ($sizeof * 8 - 1));
    }
    if ($bit > $sizeof * 8) {
        throw new \OverflowException("Bit must be in range 0-" . ($sizeof * 8 - 1));
    }

    $byte = $bit >> 3;
    $bit &= 7;
    $bitmask = 1 << $bit;

    $a[$byte] = \chr(\ord($a[$byte]) | $bitmask);

    return $a;
}

function unset_bit(string $a, int $bit): string
{
    $sizeof = \strlen($a);
    if ($bit < 0) {
        throw new \UnderflowException("Bit must be in range 0-" . ($sizeof * 8 - 1));
    }
    if ($bit > $sizeof * 8) {
        throw new \OverflowException("Bit must be in range 0-" . ($sizeof * 8 - 1));
    }

    $byte = $bit >> 3;
    $bit &= 7;
    $bitmask = 1 << $bit;

    $a[$byte] = \chr(\ord($a[$byte]) & ~$bitmask);

    return $a;
}

function is_bit_set(string $a, int $bit): bool
{
    $sizeof = \strlen($a);
    if ($bit < 0) {
        throw new \UnderflowException("Bit must be in range 0-" . ($sizeof * 8 - 1));
    }
    if ($bit > $sizeof * 8) {
        throw new \OverflowException("Bit must be in range 0-" . ($sizeof * 8 - 1));
    }

    $byte = $bit >> 3;
    $bit &= 7;
    $bitmask = 1 << $bit;

    return (\ord($a[$byte]) & $bitmask) > 1;
}
