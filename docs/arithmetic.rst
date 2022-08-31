Arithmetic
##########

.. php:namespace:: Arokettu\Unsigned

Bit Shifts
==========

.. php:function:: shift_left(string $value, int $shift): string

    ``$value << $shift``

.. php:function:: shift_right(string $value, int $shift): string

    ``$value >> $shift``

Addition and Subtraction
========================

.. php:function:: add(string $a, string $b): string

    ``$a + $b``

.. php:function:: add_int(string $a, int $b): string

    ``$a + $b`` optimized for small integers

.. php:function:: sub(string $a, string $b): string

    ``$a - $b``

.. php:function:: sub_int(string $a, int $b): string

    ``$a - $b`` optimized for small subtrahends

.. php:function:: sub_int_rev(int $a, string $b): string

    ``$a - $b`` optimized for small minuends

Multiplication
==============

.. php:function:: mul(string $a, string $b): string

    ``$a * $b``

.. php:function:: mul_int(string $a, int $b): string

    ``$a * $b`` optimized for small integers

Division
========

.. php:function:: div_mod(string $a, string $b): [string, string]

    ``$a /% $b``

    :return: [quotient, remainder]

.. php:function:: div_mod_int(string $a, int $b): [string, int]

    ``$a /% $b`` optimized for small divisors

    :return: [quotient, remainder]

.. php:function:: div(string $a, string $b): string

    ``$a / $b``

.. php:function:: div_int(string $a, int $b): string

    ``$a / $b`` optimized for small divisors

.. php:function:: mod(string $a, string $b): string

    ``$a % $b``

.. php:function:: mod_int(string $a, int $b): int

    ``$a % $b`` optimized for small divisors

    :return: Result is integer because if $b can be represented as native integer, remainder can be too

Comparison
==========

.. php:function:: compare(string $a, compare $b): int

    ``$a <=> $b``

    :return: Same values as the spaceship operator

Bit manipulation
================

.. php:function:: is_bit_set(string $a, int $bit): bool

    :param int $bit: Bit number, 0 is the least significant bit
    :return: If ``$bit``'th bit is set

.. php:function:: set_bit(string $a, int $bit): string

    Set ``$bit``'th bit to 1

    :param int $bit: Bit number, 0 is the least significant bit

.. php:function:: unset_bit(string $a, int $bit): string

    Set ``$bit``'th bit to 0

    :param int $bit: Bit number, 0 is the least significant bit
