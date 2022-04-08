<?php

declare(strict_types=1);

namespace Hereldar\Results;

use Closure;
use Hereldar\Results\Exceptions\UndefinedException;
use Hereldar\Results\Exceptions\UnusedResult;
use Hereldar\Results\Interfaces\IResult;
use Throwable;

/**
 * @template T
 * @template E of Throwable|null
 *
 * @implements IResult<T, E>
 */
abstract class AbstractResult implements IResult
{
    protected bool $used = false;
    private readonly Backtrace $trace;

    /**
     * @param T $value
     * @param E $exception
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
     * @template F of Throwable|null
     *
     * @param IResult<U, F>|Closure(T):IResult<U, F> $result
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
     * @return E
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

    abstract public function isError(): bool;

    abstract public function isOk(): bool;

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
     * @template F of Throwable|null
     *
     * @param IResult<U, F>|Closure():IResult<U, F> $result
     *
     * @return static|IResult<U, F>
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
            throw new UndefinedException($this);
        }

        throw $this->exception;
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
