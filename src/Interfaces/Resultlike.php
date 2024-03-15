<?php

declare(strict_types=1);

namespace Hereldar\Results\Interfaces;

use Closure;
use Hereldar\Results\Error;
use Hereldar\Results\Ok;
use Throwable;

/**
 * @internal
 */
interface Resultlike
{
    /**
     * Returns the given `result` if this instance is a success.
     * Otherwise, returns this error instance.
     *
     * **Note:** If the `result` is a closure and this instance
     * is a success, this method will call it and return its output.
     */
    public function andThen(Ok|Error|Closure $result): Ok|Error;

    /**
     * Returns `true` if the result contains a value.
     */
    public function hasValue(): bool;

    /**
     * Returns `true` if the result is an error.
     */
    public function isError(): bool;

    /**
     * Returns `true` if the result is a success.
     */
    public function isOk(): bool;

    /**
     * Performs the given `action` on the encapsulated value if this
     * instance is an error. Returns the original instance unchanged.
     *
     * @param Closure(mixed):mixed $action
     *
     * @return $this
     */
    public function onFailure(Closure $action): static;

    /**
     * Performs the given `action` on the encapsulated value if this
     * instance is a success. Returns the original instance unchanged.
     *
     * @param Closure(mixed):mixed $action
     *
     * @return $this
     */
    public function onSuccess(Closure $action): static;

    /**
     * Returns `value` if the result is an error. Otherwise, returns
     * the success value.
     *
     * **Note:** If `value` is a closure and the result is an error,
     * this method will call it and return the output.
     */
    public function or(mixed $value): mixed;

    /**
     * Terminates execution of the script if the result is an error.
     * Otherwise, returns the success value.
     *
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function orDie(int|string|null $status = null): mixed;

    /**
     * Returns the given `result` if this instance is an error.
     * Otherwise, returns this success instance.
     *
     * **Note:** If the `result` is a closure and this instance
     * is an error, this method will call it and return its output.
     */
    public function orElse(Ok|Error|Closure $result): Ok|Error;

    /**
     * Throws an exception if the result is an error. Otherwise,
     * returns the success value.
     */
    public function orFail(): mixed;

    /**
     * Returns `false` if the result is an error. Otherwise, returns
     * the success value.
     */
    public function orFalse(): mixed;

    /**
     * Returns `null` if the result is an error. Otherwise, returns
     * the success value.
     */
    public function orNull(): mixed;

    /**
     * Throws the given exception if the result is an error.
     * Otherwise, returns the success value.
     *
     * **Note:** If `exception` is a closure and the result is an
     * error, this method will call it and throw the output.
     */
    public function orThrow(Throwable|Closure $exception): mixed;

    /**
     * Returns the result's value, if any.
     */
    public function value(): mixed;
}
