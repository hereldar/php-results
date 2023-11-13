<?php

declare(strict_types=1);

namespace Hereldar\Results;

use Closure;
use Throwable;

/**
 * Factory class to make new `Ok` and `Error` instances.
 *
 * This class cannot be instantiated.
 */
final class Result
{
    private function __construct() {}

    /**
     * Makes an `Ok` with the given `value`.
     *
     * **Note:** If `value` is a closure, this method will call it and
     * use the returned value to make the result, returning an `Error`
     * if any exception is thrown.
     *
     * @template U
     *
     * @param U|Closure():U $value
     *
     * @return Ok<U>|Error<Throwable>
     *
     * @psalm-suppress MixedAssignment
     */
    public static function of(mixed $value): Ok|Error
    {
        if ($value instanceof Closure) {
            try {
                $value = $value();
            } catch (Throwable $e) {
                return Error::of($e);
            }
        }

        return Ok::of($value);
    }

    /**
     * Makes an empty `Error` if the value is `null`. Otherwise, makes
     * an `Ok` with the given `value`.
     *
     * **Note:** If `value` is a closure, this method will call it and
     * use the returned value to make the result.
     *
     * @template U
     *
     * @param (U|null)|Closure():(U|null) $value
     *
     * @return Ok<U>|Error<null>
     *
     * @psalm-suppress MixedAssignment
     */
    public static function fromNullable(mixed $value): Ok|Error
    {
        if ($value instanceof Closure) {
            $value = $value();
        }

        return ($value === null)
            ? Error::empty()
            : Ok::of($value);
    }

    /**
     * Makes an empty `Error` if the value is `false`. Otherwise,
     * makes an `Ok` with the given `value`.
     *
     * **Note:** If `value` is a closure, this method will call it and
     * use the returned value to make the result.
     *
     * @template U
     *
     * @param (U|false)|Closure():(U|false) $value
     *
     * @return Ok<U>|Error<null>
     *
     * @psalm-suppress MixedAssignment
     */
    public static function fromFalsable(mixed $value): Ok|Error
    {
        if ($value instanceof Closure) {
            $value = $value();
        }

        return ($value === false)
            ? Error::empty()
            : Ok::of($value);
    }
}
