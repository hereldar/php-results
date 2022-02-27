<?php

declare(strict_types=1);

namespace Hereldar\Results\Interfaces;

use Closure;
use RuntimeException;

interface IResult
{
    /**
     * Returns `default` if the result is a success. Otherwise,
     * returns the error result.
     *
     * **Note:** If `default` is a closure and the result is a
     * success, this method will call it and return its output.
     *
     * @param IResult|Closure $default
     * @return IResult
     */
    public function andThen(IResult|Closure $default): IResult;

    /**
     * Returns `true` if the result provides a message.
     *
     * @return bool
     */
    public function hasMessage(): bool;

    /**
     * Returns `true` if the result contains a value.
     *
     * @return bool
     */
    public function hasValue(): bool;

    /**
     * Returns `true` if the result is an error.
     *
     * @return bool
     */
    public function isError(): bool;

    /**
     * Returns `true` if the result is a success.
     *
     * @return bool
     */
    public function isOk(): bool;

    /**
     * Returns the result's message, if any.
     *
     * @return string
     */
    public function message(): string;

    /**
     * Returns `default` if the result is an error. Otherwise,
     * returns the success value.
     *
     * **Note:** If `default` is a closure and the result is an error,
     * this method will call it and return the output.
     *
     * @param mixed|Closure $default
     * @return mixed
     */
    public function or(mixed $default): mixed;

    /**
     * Terminates execution of the script if the result is an error. Otherwise, returns the success value.
     *
     * @param int|string|null $status
     * @return mixed
     */
    public function orDie(int|string $status = null): mixed;

    /**
     * Returns `default` if the result is an error. Otherwise, returns
     * the success result.
     *
     * **Note:** If `default` is a closure and the result is an error,
     * this method will call it and return its output.
     *
     * @param IResult|Closure $default
     * @return IResult
     */
    public function orElse(IResult|Closure $default): IResult;

    /**
     * Throws an exception if the result is an error. Otherwise,
     * returns the success value.
     *
     * @return mixed
     *
     * @throws RuntimeException
     */
    public function orFail(): mixed;

    /**
     * Returns `null` if the result is an error. Otherwise, returns
     * the success value.
     *
     * @return mixed|null
     */
    public function orNull(): mixed;

    /**
     * Returns the result's value, if any.
     *
     * @return mixed|null
     */
    public function value(): mixed;
}
