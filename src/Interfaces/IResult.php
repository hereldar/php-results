<?php

declare(strict_types=1);

namespace Hereldar\Results\Interfaces;

use Closure;
use RuntimeException;

/**
 * @template T
 */
interface IResult
{
    /**
     * Returns `default` if the result is a success. Otherwise,
     * returns the error result.
     *
     * **Note:** If `default` is a closure and the result is a
     * success, this method will call it and return its output.
     *
     * @template T2
     *
     * @param IResult<T2>|Closure(T):IResult<T2> $default
     *
     * @return IResult<T2>|$this
     */
    public function andThen(IResult|Closure $default): IResult;

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
     * Returns `default` if the result is an error. Otherwise,
     * returns the success value.
     *
     * **Note:** If `default` is a closure and the result is an error,
     * this method will call it and return the output.
     *
     * @template T2
     *
     * @param T2|Closure():T2 $default
     *
     * @return T|T2
     */
    public function or(mixed $default): mixed;

    /**
     * Terminates execution of the script if the result is an error.
     * Otherwise, returns the success value.
     *
     * @return T
     */
    public function orDie(int|string $status = null): mixed;

    /**
     * Returns `default` if the result is an error. Otherwise, returns
     * the success result.
     *
     * **Note:** If `default` is a closure and the result is an error,
     * this method will call it and return its output.
     *
     * @template T2
     *
     * @param IResult<T2>|Closure():IResult<T2> $default
     *
     * @return $this|IResult<T2>
     */
    public function orElse(IResult|Closure $default): IResult;

    /**
     * Throws an exception if the result is an error. Otherwise,
     * returns the success value.
     *
     * @throws RuntimeException
     *
     * @return T
     */
    public function orFail(): mixed;

    /**
     * Returns `null` if the result is an error. Otherwise, returns
     * the success value.
     *
     * @return T|null
     */
    public function orNull(): mixed;

    /**
     * Returns the result's value, if any.
     *
     * @return T|null
     */
    public function value(): mixed;
}
