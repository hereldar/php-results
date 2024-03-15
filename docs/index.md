
Getting Started
===============

`Hereldar\Results` includes an opinionated version of the `Result` type of Rust.
It is not intended to replicate the original type one-to-one, but to allow
developers to handle the results in any way they choose.

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

Development
-----------

Run the following commands from the project folder:

```bash
make tests
make static-analysis
make coding-standards
```

To execute:

- A [PHPUnit](https://phpunit.de) test suite.
- [PHPStan](https://phpstan.org/) and [Psalm](https://psalm.dev/) for
  static code analysis.
- [Easy Coding Standard](https://github.com/easy-coding-standard/easy-coding-standard)
  to fix coding standards.
