# Changelog

## 1.x

### 1.3.1

*Jan 25, 2024*

* Fixed `to_base(..., 16)` generating values with leading zeros while other bases do not

### 1.3.0

*Dec 17, 2023*

* Exception rework.
  The library now throws DomainException for wrong arguments and RangeException for arithmetic problems.

### 1.2.2

*Oct 28, 2023*

* Fixed mul signature: internal-only param is removed

### 1.2.1

*Sep 5, 2023*

* Fixed argument corruption on fallback from mul_int to mul

### 1.2.0

*Jul 8, 2023*

* Added functions to convert to and from other number bases

### 1.1.0

*Mar 9, 2023*

* Functions to convert to and from decimal strings

### 1.0.2

*Sep 15, 2022*

* Multiplication is now 50% faster on 64-bit systems

### 1.0.1

*Sep 14, 2022*

* Fixed integer overflow in div_mod_int and mod_int

### 1.0.0

*Sep 1, 2022*

Re-release 0.5.0 as a final release

## 0.x

### 0.5.0

*Aug 31, 2022*

* fits_into_int(string)
* to_signed_int(string)
* Multiplication and division optimizations for small arguments

### 0.4.1

*Aug 30, 2022*

* Fixed invalid mod length

### 0.4.0

*Aug 29, 2022*

* New functions:
  * div_mod(string, string)
  * div_mod_int(string, int)
  * div_(string, string)
  * div_int(string, int)
  * mod(string, string)
  * mod_int(string, int)
  * compare(string, string)
  * set_bit(string, int)
  * unset_bit(string, int)
  * is_bit_set(string, int)

### 0.3.0

*Aug 29, 2022*

* New functions:
  * add_int(string, int)
  * sub_int(string, int)
  * sub_int_rev(int, string)
  * neg(string)
  * mul_int(string, int)

### 0.2.1

*Aug 26, 2022*

* Relax from_* methods: truncate values instead of raising exceptions

### 0.2.0

*Aug 26, 2022*

* Added subtraction
* Fixed conversion of negative integers when target int size is smaller than platform int size
* Fixed detection of negative shifts

### 0.1.1

*Aug 26, 2022*

* Zero import fix

### 0.1.0

*Aug 26, 2022*

Initial release
