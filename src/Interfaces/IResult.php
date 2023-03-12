<?php

declare(strict_types=1);

namespace Hereldar\Results\Interfaces;

use Closure;
use Throwable;

/**
 * @template T
 * @template E of ?Throwable
 */
interface IResult
{
    /**
     * Returns the given `result` if this instance is a success.
     * Otherwise, returns this error instance.
     *
     * **Note:** If the given `result` is a closure and this instance
     * is a success, this method will call it and return its output.
     *
     * @template U
     * @template F of ?Throwable
     *
     * @param IResult<U, F>|Closure(T=):IResult<U, F> $result
     *
     * @return $this|IResult<U, F>
     */
    public function andThen(IResult|Closure $result): self;

    /**
     * Returns the result's exception, if any.
     *
     * @return E
     */
    public function exception(): ?Throwable;

    /**
     * Returns `true` if the result includes an exception.
     */
    public function hasException(): bool;

    /**
     * Returns `true` if the result provides a message.
     */
    public function hasMessage(): bool;

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
     * Returns the result's message, if any.
     */
    public function message(): string;

    /**
     * Performs the given `action` on the encapsulated `Throwable`
     * exception if this instance is an error. Returns the original
     * instance unchanged.
     *
     * @param Closure(E=):void $action
     *
     * @return $this
     */
    public function onFailure(Closure $action): static;

    /**
     * Performs the given `action` on the encapsulated value if this
     * instance is a success. Returns the original instance unchanged.
     *
     * @param Closure(T=):void $action
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
     *
     * @template U
     *
     * @param U|Closure(T=):U $value
     *
     * @return T|U
     */
    public function or(mixed $value): mixed;

    /**
     * Terminates execution of the script if the result is an error.
     * Otherwise, returns the success value.
     *
     * @return T
     */
    public function orDie(int|string $status = null): mixed;

    /**
     * Returns the given `result` if this instance is an error.
     * Otherwise, returns this success instance.
     *
     * **Note:** If the given `result` is a closure and this instance
     * is an error, this method will call it and return its output.
     *
     * @template U
     * @template F of ?Throwable
     *
     * @param IResult<U, F>|Closure(T=):IResult<U, F> $result
     *
     * @return $this|IResult<U, F>
     */
    public function orElse(IResult|Closure $result): self;

    /**
     * Throws an exception if the result is an error. Otherwise,
     * returns the success value.
     *
     * @throws E
     *
     * @return T
     *
     * @psalm-suppress UndefinedDocblockClass
     */
    public function orFail(): mixed;

    /**
     * Returns `false` if the result is an error. Otherwise, returns
     * the success value.
     *
     * @return T|false
     */
    public function orFalse(): mixed;

    /**
     * Returns `null` if the result is an error. Otherwise, returns
     * the success value.
     *
     * @return T|null
     */
    public function orNull(): mixed;

    /**
     * Throws the given exception if the result is an error.
     * Otherwise, returns the success value.
     *
     * **Note:** If `exception` is a closure and the result is an
     * error, this method will call it and throw the output.
     *
     * @template F of Throwable
     *
     * @param F|Closure(E=):F $exception
     *
     * @throws F
     *
     * @return T
     *
     * @psalm-suppress UndefinedDocblockClass
     */
    public function orThrow(Throwable|Closure $exception): mixed;

    /**
     * Returns the result's value, if any.
     *
     * @return T
     */
    public function value(): mixed;
}
