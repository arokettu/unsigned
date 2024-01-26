# Unsigned Arithmetic for PHP

[![Packagist](https://img.shields.io/packagist/v/arokettu/unsigned.svg?style=flat-square)](https://packagist.org/packages/arokettu/unsigned)
[![PHP](https://img.shields.io/packagist/php-v/arokettu/unsigned.svg?style=flat-square)](https://packagist.org/packages/arokettu/unsigned)
[![Packagist](https://img.shields.io/github/license/arokettu/unsigned.svg?style=flat-square)](https://opensource.org/licenses/BSD-2-Clause)
[![Gitlab pipeline status](https://img.shields.io/gitlab/pipeline/sandfox/unsigned/master.svg?style=flat-square)](https://gitlab.com/sandfox/unsigned/-/pipelines)
[![Codecov](https://img.shields.io/codecov/c/gl/sandfox/unsigned?style=flat-square)](https://codecov.io/gl/sandfox/unsigned/)

Fixed length unsigned arithmetic emulation for PHP.
The lib was created as a helper for the random-polyfill.

## Installation

```bash
composer require 'arokettu/unsigned'
```

## Example

```php
<?php
use Arokettu\Unsigned as u;
$a = u\from_int(1234567890123456789, 24); // use 24-byte a.k.a. 192-bit arithmetic
$b = u\from_hex('123456789abcdef01234567890abcdef', 24); // numbers must have same bitness
// 1234567890123456789 * 0x123456789abcdef01234567890abcdef =
$c = u\mul($a, $b);
var_dump(u\to_dec($c)); // 29873897512945703720213152879288233401251320475301467035
```

## Documentation

Read full documentation here: <https://sandfox.dev/php/unsigned.html>

Also on Read the Docs: <https://php-unsigned.readthedocs.io/>

## Support

Please file issues on our main repo at GitLab: <https://gitlab.com/sandfox/unsigned/-/issues>

Feel free to ask any questions in our room on Gitter: <https://gitter.im/arokettu/community>

## License

The library is available as open source under the terms of the [2-Clause BSD License].

[2-Clause BSD License]: https://opensource.org/licenses/BSD-2-Clause
