
PHP Results
===========

This package includes an opinionated version of the `Result` type of Rust. It is
not intended to replicate the original type one-to-one, but to allow developers
to handle the results in any way they choose.

Use examples
------------

This `Result` type allows ignoring errors without a try-catch
block:

```php
$value = getValue()->or(false);
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

