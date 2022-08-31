Type conversion
###############

.. php:namespace:: Arokettu\Unsigned

To working string
=================

.. php:function:: from_int(int $value, int $sizeof): string

    Imports a regular PHP int.

    :param int $value: The value. Negative values are accepted but they will be cast to unsigned
    :param int $sizeof: Length of the unsigned number in bytes.
    :return: Binary string in the form this library accepts

    If ``$sizeof <= PHP_INT_SIZE``, value may be truncated.
    (but do you really need this lib then?)

.. php:function:: from_hex(string $value, int $sizeof): string

    Imports a number written as a hexadecimal string.

    :param int $value: String of hex digits
    :param int $sizeof: Length of the unsigned number in bytes
    :return: Binary string in the form this library accepts

    If ``strlen($value) >= $sizeof * 2``, value may be truncated.

From working string
===================

.. php:function:: fits_into_int(string $value): bool

    :param string $value: Binary string in the form this library accepts
    :return: If value can be represented as a native PHP integer value,
        i.e. less than ``PHP_INT_MAX``.

.. php:function:: to_int(string $value): int

    :param string $value: Binary string in the form this library accepts
    :return: The int representation

.. php:function:: to_signed_int(string $value): int

    If the most significant bit is set, return value as a negative int.

    :param string $value: Binary string in the form this library accepts
    :return: The int representation

.. php:function:: to_hex(string $value): bool

    Exports a number to a hexadecimal string.

    :param string $value: Binary string in the form this library accepts
    :return: String of hex digits
