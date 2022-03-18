<?php

declare(strict_types=1);

namespace Hereldar\Results;

use Closure;
use Hereldar\Results\Interfaces\IResult;
use RuntimeException;

/**
 * @template T
 *
 * @implements IResult<T>
 */
abstract class AbstractResult implements IResult
{
    /**
     * @param T $value
     */
    public function __construct(
        protected readonly mixed $value = null,
        protected readonly string $message = ''
    ) {
    }

    /**
     * @template T2
     *
     * @param IResult<T2>|Closure(T):IResult<T2> $default
     *
     * @return IResult<T2>|$this
     */
    public function andThen(IResult|Closure $default): IResult
    {
        if ($this->isError()) {
            return $this;
        }

        if ($default instanceof Closure) {
            return $default($this->value());
        }

        return $default;
    }

    public function hasMessage(): bool
    {
        return ($this->message !== '');
    }

    public function hasValue(): bool
    {
        return ($this->value !== null);
    }

    abstract public function isError(): bool;

    abstract public function isOk(): bool;

    public function message(): string
    {
        return $this->message;
    }

    /**
     * @template T2
     *
     * @param T2|Closure():T2 $default
     *
     * @return T|T2
     */
    public function or(mixed $default): mixed
    {
        if ($this->isOk()) {
            return $this->value;
        }

        if ($default instanceof Closure) {
            return $default();
        }

        return $default;
    }

    /**
     * @return T
     */
    public function orDie(int|string $status = null): mixed
    {
        if ($this->isOk()) {
            return null;
        }

        if (isset($status)) {
            exit($status);
        } else {
            exit;
        }
    }

    /**
     * @template T2
     *
     * @param IResult<T2>|Closure():IResult<T2> $default
     *
     * @return $this|IResult<T2>
     */
    public function orElse(IResult|Closure $default): IResult
    {
        if ($this->isOk()) {
            return $this;
        }

        if ($default instanceof Closure) {
            return $default();
        }

        return $default;
    }

    /**
     * @throws RuntimeException
     *
     * @return T
     */
    abstract public function orFail(): mixed;

    /**
     * @return T|null
     */
    public function orNull(): mixed
    {
        if ($this->isOk()) {
            return $this->value;
        }

        return null;
    }

    /**
     * @template TException of RuntimeException
     *
     * @param TException $exception
     *
     * @throws TException
     *
     * @return T
     */
    public function orThrow(RuntimeException $exception): mixed
    {
        if ($this->isOk()) {
            return null;
        }

        throw $exception;
    }

    /**
     * @return T|null
     */
    public function value(): mixed
    {
        return $this->value;
    }
}
