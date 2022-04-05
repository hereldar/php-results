<?php

declare(strict_types=1);

namespace Hereldar\Results;

use Closure;
use Hereldar\Results\Exceptions\UnusedResult;
use Hereldar\Results\Interfaces\IResult;
use RuntimeException;
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
    protected readonly ?Throwable $exception;
    private readonly string $trace;

    /**
     * @param T $value
     * @param E|string $exception
     */
    public function __construct(
        protected readonly mixed $value = null,
        Throwable|string $exception = null
    ) {
        ob_start();
        debug_print_backtrace(limit: 5);
        $this->trace = ob_get_contents();
        ob_end_clean();

        $this->exception = (is_string($exception))
            ? new RuntimeException($exception)
            : $exception;
    }

    public function __destruct()
    {
        if (!$this->used) {
            throw new UnusedResult($this, $this->trace);
        }
    }

    /**
     * @template U
     * @template F of Throwable
     *
     * @param IResult<U, F>|Closure(T):IResult<U, F> $result
     *
     * @return IResult<U, E|F>
     */
    public function andThen(IResult|Closure $result): IResult
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

    abstract public function isError(): bool;

    abstract public function isOk(): bool;

    public function message(): string
    {
        $this->used = true;

        if (!isset($this->exception)) {
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
     * @return IResult<T|U, F>
     */
    public function orElse(IResult|Closure $result): IResult
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
    public function orThrow(Throwable $exception): mixed
    {
        if ($this->isOk()) {
            return $this->value;
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
