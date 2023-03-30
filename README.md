Results
=======

[![PHP][php-badge]][php-url]
[![Code Coverage][codecov-badge]][codecov-url]
[![Type Coverage][shepherd-badge]][shepherd-url]
[![License][license-badge]][license-url]
[![Packagist][packagist-version-badge]][packagist-url]
[![Downloads][packagist-downloads-badge]][packagist-url]

[php-badge]: https://img.shields.io/badge/php-8.1%20to%208.2-777bb3.svg
[php-url]: https://coveralls.io/github/hereldar/php-results
[codecov-badge]: https://img.shields.io/codecov/c/github/hereldar/php-results
[codecov-url]: https://app.codecov.io/gh/hereldar/php-results
[coveralls-badge]: https://img.shields.io/coverallsCoverage/github/hereldar/php-results
[coveralls-url]: https://coveralls.io/github/hereldar/php-results
[shepherd-badge]: https://shepherd.dev/github/hereldar/php-results/coverage.svg
[shepherd-url]: https://shepherd.dev/github/hereldar/php-results
[license-badge]: https://img.shields.io/badge/license-MIT-brightgreen.svg
[license-url]: LICENSE
[packagist-version-badge]: https://img.shields.io/packagist/v/hereldar/results.svg
[packagist-downloads-badge]: https://img.shields.io/packagist/dt/hereldar/results.svg
[packagist-url]: https://packagist.org/packages/hereldar/results

This package includes an opinionated version of the `Result` type of Rust. It is 
not intended to replicate the original type one-to-one, but to allow developers 
to handle the results in any way they choose.

Use examples
------------

This version of the `Result` type allows ignoring errors without a try-catch 
block:

```php
$value = getValue()->or(false);
```

Also allows throwing the error as a regular exception:

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
$result = myFunction();

if ($result->isError()) {
    handleFailure($result->message());
} else {
    handleSuccess($result->value());
}

return match (true) {
    $result instanceof Ok => $result->value(),
    $result instanceof MyError => false,
    default => throw new MyException(),
}
```
