<?php

declare(strict_types=1);

namespace Arokettu\Unsigned;

function from_int(int $value, int $sizeof): string
{
    return Unsigned::from_int($value, $sizeof);
}

function to_int(string $value): int
{
    return Unsigned::to_int($value);
}

function to_signed_int(string $value): int
{
    return Unsigned::to_signed_int($value);
}

function fits_into_int(string $value): bool
{
    return Unsigned::fits_into_int($value);
}

function from_hex(string $value, int $sizeof): string
{
    return Unsigned::from_hex($value, $sizeof);
}

function to_hex(string $value): string
{
    return Unsigned::to_hex($value);
}

function from_base(string $value, int $base, int $sizeof): string
{
    return Unsigned::from_base($value, $base, $sizeof);
}

function to_base(string $value, int $base): string
{
    return Unsigned::to_base($value, $base);
}

function from_dec(string $value, int $sizeof): string
{
    return Unsigned::from_dec($value, $sizeof);
}

function to_dec(string $value): string
{
    return Unsigned::to_dec($value);
}

/**
 * $value << $shift
 */
function shift_left(string $value, int $shift): string
{
    return Unsigned::shift_left($value, $shift);
}

/**
 * $value >> $shift
 */
function shift_right(string $value, int $shift): string
{
    return Unsigned::shift_right($value, $shift);
}

/**
 * a + b
 */
function add(string $a, string $b): string
{
    return Unsigned::add($a, $b);
}

/**
 * a + int(b)
 */
function add_int(string $a, int $b): string
{
    return Unsigned::add_int($a, $b);
}

/**
 * a - b
 */
function sub(string $a, string $b): string
{
    return Unsigned::sub($a, $b);
}

/**
 * a - int(b)
 */
function sub_int(string $a, int $b): string
{
    return Unsigned::sub_int($a, $b);
}

/**
 * int(a) - b
 */
function sub_int_rev(int $a, string $b): string
{
    return Unsigned::sub_int_rev($a, $b);
}

/**
 * -a
 */
function neg(string $a): string
{
    return Unsigned::neg($a);
}

/**
 * a * b
 */
function mul(string $a, string $b): string
{
    return Unsigned::mul($a, $b);
}

/**
 * a * int(b)
 */
function mul_int(string $a, int $b): string
{
    return Unsigned::mul_int($a, $b);
}

/**
 * a / b, a % b
 *
 * @return array [div -> string, mod -> string]
 */
function div_mod(string $a, string $b): array
{
    return Unsigned::div_mod($a, $b);
}

/**
 * a / int(b), a % int(b)
 *
 * @return array [div -> string, mod -> int]
 */
function div_mod_int(string $a, int $b): array
{
    return Unsigned::div_mod_int($a, $b);
}

/**
 * a / b
 */
function div(string $a, string $b): string
{
    return Unsigned::div($a, $b);
}

/**
 * a / int(b)
 */
function div_int(string $a, int $b): string
{
    return Unsigned::div_int($a, $b);
}

/**
 * a % b
 */
function mod(string $a, string $b): string
{
    return Unsigned::mod($a, $b);
}

/**
 * a % int(b) -> int
 */
function mod_int(string $a, int $b): int
{
    return Unsigned::mod_int($a, $b);
}

/**
 * a <=> b
 */
function compare(string $a, string $b): int
{
    return Unsigned::compare($a, $b);
}

function set_bit(string $a, int $bit): string
{
    return Unsigned::set_bit($a, $bit);
}

function unset_bit(string $a, int $bit): string
{
    return Unsigned::unset_bit($a, $bit);
}

function is_bit_set(string $a, int $bit): bool
{
    return Unsigned::is_bit_set($a, $bit);
}
