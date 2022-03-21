Results
=======

This package includes an opinionated version of the `Result` type of Rust. It is 
not intended to replicate the original type one-to-one, but to allow developers 
to handle the results in any way they choose.

Use examples
------------

This version of the `Result` type allows ignoring errors without a try-catch 
block:

```
$value = getValue()->or(false);
```

Also allows throwing the error as a regular exception:

```
doSomething()->orFail();
```

Or throwing a custom exception:

```
doSomething()->orThrow(new MyCustomException());
```

More complex flows can be handled by concatenating operations:

```
$value = fetchRecord()->andThen(updateIt(...))->orElse(insertIt(...));
```

And much more:

```
$result = myFunction();

if ($result->isError()) {
    ...
} else {
    ...
}

match (true) {
    $result instanceof Ok => ...,
    $result instanceof MyError => ...,
    default => ...,
}
```
