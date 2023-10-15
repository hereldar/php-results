Results
=======

[![PHP][php-badge]][php-url]
[![Code Coverage][codecov-badge]][codecov-url]
[![Type Coverage][shepherd-coverage-badge]][shepherd-url]
[![Psalm Level][shepherd-level-badge]][shepherd-url]
[![Packagist][packagist-version-badge]][packagist-url]
[![License][license-badge]][license-url]

[php-badge]: https://img.shields.io/badge/php-8.1%20to%208.3-777bb3.svg
[php-url]: https://coveralls.io/github/hereldar/php-results
[codecov-badge]: https://img.shields.io/codecov/c/github/hereldar/php-results
[codecov-url]: https://app.codecov.io/gh/hereldar/php-results
[coveralls-badge]: https://img.shields.io/coverallsCoverage/github/hereldar/php-results
[coveralls-url]: https://coveralls.io/github/hereldar/php-results
[shepherd-coverage-badge]: https://shepherd.dev/github/hereldar/php-results/coverage.svg
[shepherd-level-badge]: https://shepherd.dev/github/hereldar/php-results/level.svg
[shepherd-url]: https://shepherd.dev/github/hereldar/php-results
[packagist-version-badge]: https://img.shields.io/packagist/v/hereldar/results.svg
[packagist-downloads-badge]: https://img.shields.io/packagist/dt/hereldar/results.svg
[packagist-url]: https://packagist.org/packages/hereldar/results
[license-badge]: https://img.shields.io/badge/license-MIT-brightgreen.svg
[license-url]: LICENSE

This package includes an opinionated version of the `Result` type of 
Rust. It is not intended to replicate the original type one-to-one, 
but to allow developers to handle the results in any way they choose.

Use examples
------------

This `Result` type allows ignoring errors without a try-catch block:

```php
$value = getValue()->or($default);
```

It also allows throwing the error as a regular exception:

```php
doSomething()->orFail();
```

Or throwing a custom exception:

```php
doSomething()->orThrow(new MyException());
```

More complex flows can be handled by concatenating operations:

```php
$record = fetchRecord()
    ->andThen(updateIt(...))
    ->orElse(insertIt(...))
    ->orFail();
```

And much more:

```php
doSomething()
    ->onFailure(logFailure(...))
    ->onSuccess(logSuccess(...))
    ->onSuccess(doSomethingElse(...));
```

Installation
------------

Via Composer:

```bash
composer require hereldar/results
```

Testing
-------

Run the following command from the project folder:

```bash
composer test
```

To execute:

- A [PHPUnit](https://phpunit.de) test suite.
- [PHPStan](https://phpstan.org/) and [Psalm](https://psalm.dev/) for
  static code analysis.
- [PHP CS Fixer](https://github.com/PHP-CS-Fixer/PHP-CS-Fixer) to fix
  coding standards.

Documentation
-------------

- [Guide](https://hereldar.github.io/php-results/)
- [Reference](https://hereldar.github.io/php-results/reference/)

Credits
-------

- [Samuel Maudo](https://github.com/samuelmaudo)

License
-------

The MIT License (MIT). Please see [LICENSE](LICENSE) for more information.
