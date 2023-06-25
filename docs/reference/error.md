
# Error


Contains the error exception.

Instances of this class are immutable and not affected by any
method calls.


## Static Methods


### empty

```php
public static function empty(): self;
```

Makes a new `Error` containing a `RuntimeException` with no
message.


### withException

```php
public static function withException(Throwable $exception): self;
```

Makes a new `Error` with the given `exception`.


### withMessage

```php
public static function withMessage(string $message): self;
```

Makes a new `Error` containing a `RuntimeException` with the
given `message`.


## Methods


### andThen

```php
public function andThen(Ok|Error|Closure $result): self;
```

Returns the original instance, ignoring the given `result`.


### exception

```php
public function exception(): Throwable;
```

Returns the error exception.


### hasException

```php
public function hasException(): true;
```

Returns `true`, since `Error` instances always contain an exception.


### hasMessage

```php
public function hasMessage(): bool;
```

Returns whether the result provides a message.


### hasValue

```php
public function hasValue(): false;
```

Returns `false`, since `Error` instances never contain a value.


### isError

```php
public function isError(): true;
```

Returns whether the result is an error.


### isOk

```php
public function isOk(): false;
```

Returns whether the result is a success.


### message

```php
public function message(): string;
```

Returns the result's message, if any.


### onFailure

```php
public function onFailure(Closure $action): self;
```

Performs the given `action` on the encapsulated value. Returns the
original instance unchanged.

**Parameters:**

`$action` a `Closure` that receives the error exception.


### onSuccess

```php
public function onSuccess(Closure $action): self;
```

Returns the original instance without performing the given `action`.

**Parameters:**

`$action` a `Closure` that receives the success value.


### or

```php
public function or(mixed $value): mixed;
```

Returns given `value`.

**Note:** If the `value` is a closure, this method will call it
and return the output.


### orDie

```php
public function orDie(int|string $status = null): never;
```

Terminates execution of the script.


### orElse

```php
public function orElse(Ok|Error|Closure $result): Ok|Error;
```

Returns the given `result`.

**Note:** If the `result` is a closure, this method will call it
and return its output.


### orFail

```php
public function orFail(): never;
```

Throws the error exception.


### orFalse

```php
public function orFalse(): false;
```

Returns `false`.


### orNull

```php
public function orNull(): null;
```

Returns `null`.


### orThrow

```php
public function orThrow(Throwable|Closure $exception): never;
```

Throws the given exception.

**Note:** If `exception` is a closure, this method will call it
and throw the output.


### value

```php
public function value(): null;
```

Returns `null`, since `Error` instances never contain a value.
