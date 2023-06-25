
# Ok


Contains the success value.

Instances of this class are immutable and not affected by any
method calls.


## Static Methods


### empty

```php
public static function empty(): self;
```

Returns a successful result containing no value (`null`).


### withValue

```php
public static function withValue(mixed $value): self;
```

Makes a new `Ok` with the given `value`.


## Methods


### andThen

```php
public function andThen(Ok|Error|Closure $result): Ok|Error;
```

Returns the given `result`.

**Note:** If the `result` is a closure, this method will call it
and return its output.


### exception

```php
public function exception(): null;
```

Returns `null`, since `Ok` instances never contain an exception.


### hasException

```php
public function hasException(): false;
```

Returns `false`, since `Ok` instances never contain an exception.


### hasMessage

```php
public function hasMessage(): false;
```

Returns `false`, since `Ok` instances never contain a message.


### hasValue

```php
public function hasValue(): bool;
```

Returns `true` if the result contains a value.


### isError

```php
public function isError(): false;
```

Returns whether the result is an error.


### isOk

```php
public function isOk(): true;
```

Returns whether the result is a success.


### message

```php
public function message(): string;
```

Returns an empty string, since `Ok` instances never contain a message.


### onFailure

```php
public function onFailure(Closure $action): self;
```

Returns the original instance without performing the given `action`.

**Parameters:**

`$action` a `Closure` that receives the error exception.


### onSuccess

```php
public function onSuccess(Closure $action): self;
```

Performs the given `action` on the encapsulated value. Returns the
original instance unchanged.

**Parameters:**

`$action` a `Closure` that receives the success value.


### or

```php
public function or(mixed $value): mixed;
```

Returns the success value, ignoring the given `value`.


### orDie

```php
public function orDie(int|string $status = null): mixed;
```

Returns the success value.


### orElse

```php
public function orElse(Ok|Error|Closure $result): self;
```

Returns the original instance, ignoring the given `result`.


### orFail

```php
public function orFail(): mixed;
```

Returns the success value.


### orFalse

```php
public function orFalse(): mixed;
```

Returns the success value.


### orNull

```php
public function orNull(): mixed;
```

Returns the success value.


### orThrow

```php
public function orThrow(Throwable|Closure $exception): mixed;
```

Returns the success value.


### value

```php
public function value(): mixed;
```

Returns the result's value, if any.
