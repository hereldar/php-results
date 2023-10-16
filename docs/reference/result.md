
# Result


Factory class to make new `Ok` and `Error` instances.

This class cannot be instantiated.


## Static Methods


### of

```php
public static function of(mixed $value): Ok|Error;
```

Makes an `Ok` with the given `value`.

**Note:** If `value` is a closure, this method will call it and
use the returned value to make the result, returning an `Error`
if any exception is thrown.


### fromFalsable

```php
public static function fromFalsable(mixed $value): Ok|Error;
```

Makes an empty `Error` if the value is `false`. Otherwise,
makes an `Ok` with the given `value`.

**Note:** If `value` is a closure, this method will call it and
use the returned value to make the result.


### fromNullable

```php
public static function fromNullable(mixed $value): Ok|Error;
```

Makes an empty `Error` if the value is `null`. Otherwise,
makes an `Ok` with the given `value`.

**Note:** If `value` is a closure, this method will call it and
use the returned value to make the result.
