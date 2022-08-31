Data Type
#########

Explanation and Compatibility
=============================

The main data type is a fixed length string with binary data.
Length in bytes can be arbitrary but in binary operations both arguments must be of the same length.

Strings contain integers in binary little endian form.
It is interoperable with GMP and ``brick/math`` like so:

.. code:: php

    <?php

    $u = \Arokettu\Unsigned\from_int(123, 16);

    var_dump(gmp_import($u, 1, GMP_LITTLE_ENDIAN | GMP_LSW_FIRST)); // 123
    var_dump(\Brick\Math\BigInteger::fromBytes(
        strrev($u), // brick/math is big endian
        false       // arokettu/unsigned is always unsigned
    )); // 123

PHP Operators
=============

.. note:: https://www.php.net/manual/en/language.operators.bitwise.php

Bitwise operators (except for shifts) work on strings and this was the main idea behind this library.

.. code:: php

    <?php

    $a = from_int(0b1111000011110000, 3);
    $b = from_int(0b1100110011001100, 3);

    var_dump(decbin(to_int($a | $b))); // 1111110011111100
    var_dump(decbin(to_int($a & $b))); // 1100000011000000
    var_dump(decbin(to_int($a ^ $b))); // 0011110000111100, strings must have same length
    var_dump(decbin(to_int(~$a))); // 111111110000111100001111
