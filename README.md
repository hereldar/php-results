Results
=======

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

Results must be used
--------------------

One of the great features of Rust results is that its compiler requires you to 
use each returned result.

In PHP there is no compile step, but it is possible to throw an exception from 
the `__destruct()` method if none of the methods of the result have been called.

It's a bit risky, since the PHP documentation says that throwing an exception 
from a destructor could cause a fatal error (if called at script termination 
time).

However, forgetting to handle a result is also risky, and I haven't found a 
better way to warn developers of unused results.
