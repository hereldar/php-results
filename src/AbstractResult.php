<?php

declare(strict_types=1);

namespace Hereldar\Results;

use Closure;
use Hereldar\Results\Exceptions\MissingException;
use Hereldar\Results\Exceptions\UnusedResult;
use Hereldar\Results\Interfaces\IResult;
use Throwable;

/**
 * @template T
 * @template E of Throwable
 *
 * @implements IResult<T, E>
 */
abstract class AbstractResult implements IResult
{
    protected bool $used = false;
    private readonly Backtrace $trace;

    /**
     * @param T $value
     * @param E|null $exception
     */
    public function __construct(
        protected readonly mixed $value = null,
        protected readonly ?Throwable $exception = null
    ) {
        $this->trace = new Backtrace($this::class);
    }

    public function __destruct()
    {
        if (!$this->used) {
            throw new UnusedResult($this, (string) $this->trace);
        }
    }

    /**
     * @template U
     * @template F of Throwable
     *
     * @param IResult<U, F>|Closure(T|null):IResult<U, F> $result
     *
     * @return $this|IResult<U, F>
     */
    public function andThen(IResult|Closure $result): static|IResult
    {
        if ($this->isError()) {
            return $this;
        }

        if ($result instanceof Closure) {
            return $result($this->value());
        }

        return $result;
    }

    /**
     * @return E|null
     */
    public function exception(): ?Throwable
    {
        $this->used = true;

        return $this->exception;
    }

    public function hasException(): bool
    {
        $this->used = true;

        return isset($this->exception);
    }

    public function hasMessage(): bool
    {
        return ($this->message() !== '');
    }

    public function hasValue(): bool
    {
        $this->used = true;

        return ($this->value !== null);
    }

    public function isError(): bool
    {
        $this->used = true;

        return ($this->exception !== null);
    }

    public function isOk(): bool
    {
        $this->used = true;

        return (null === $this->exception);
    }

    public function message(): string
    {
        $this->used = true;

        if (null === $this->exception) {
            return '';
        }

        return $this->exception->getMessage();
    }

    /**
     * @template U
     *
     * @param U|Closure():U $value
     *
     * @return T|U
     */
    public function or(mixed $value): mixed
    {
        if ($this->isOk()) {
            return $this->value;
        }

        if ($value instanceof Closure) {
            return $value();
        }

        return $value;
    }

    /**
     * @return T
     */
    public function orDie(int|string $status = null): mixed
    {
        if ($this->isOk()) {
            return $this->value;
        }

        if (isset($status)) {
            exit($status);
        } else {
            exit;
        }
    }

    /**
     * @template U
     * @template F of Throwable
     *
     * @param IResult<U, F>|Closure():IResult<U, F> $result
     *
     * @return $this|IResult<U, F>
     */
    public function orElse(IResult|Closure $result): static|IResult
    {
        if ($this->isOk()) {
            return $this;
        }

        if ($result instanceof Closure) {
            return $result();
        }

        return $result;
    }

    /**
     * @throws E
     *
     * @return T
     */
    public function orFail(): mixed
    {
        if ($this->isOk()) {
            return $this->value;
        }

        if (null === $this->exception) {
            throw new MissingException($this);
        }

        throw $this->exception;
    }

    /**
     * @return T|false
     */
    public function orFalse(): mixed
    {
        if ($this->isOk()) {
            return $this->value;
        }

        return false;
    }

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
     * @template F of Throwable
     *
     * @param F|Closure():F $exception
     *
     * @throws F
     *
     * @return T
     */
    public function orThrow(Throwable|Closure $exception): mixed
    {
        if ($this->isOk()) {
            return $this->value;
        }

        if ($exception instanceof Closure) {
            throw $exception();
        }

        throw $exception;
    }

    /**
     * @return T|null
     */
    public function value(): mixed
    {
        $this->used = true;

        return $this->value;
    }
}
