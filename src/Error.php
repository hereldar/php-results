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
final class Error implements IResult
{
    private bool $used = false;
    private readonly Backtrace $trace;

    /**
     * @param E $exception
     */
    public function __construct(
        private readonly Throwable $exception
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
     * @return self<T, RuntimeException>
     */
    public static function empty(): self
    {
        return new self(new RuntimeException());
    }

    /**
     * @template F of Throwable
     *
     * @param F $exception
     *
     * @return self<T, F>
     */
    public static function withException(Throwable $exception): self
    {
        return new self($exception);
    }

    /**
     * @return self<T, RuntimeException>
     */
    public static function withMessage(string $message): self
    {
        return new self(new RuntimeException($message));
    }

    /**
     * @template U
     * @template F of Throwable
     *
     * @param IResult<U, F>|Closure(null):IResult<U, F> $result
     *
     * @return $this
     */
    public function andThen(IResult|Closure $result): self
    {
        $this->used = true;

        return $this;
    }

    /**
     * @return E
     */
    public function exception(): Throwable
    {
        $this->used = true;

        return $this->exception;
    }

    public function hasException(): bool
    {
        $this->used = true;

        return true;
    }

    public function hasMessage(): bool
    {
        $this->used = true;

        return ($this->exception->getMessage() !== '');
    }

    public function hasValue(): bool
    {
        $this->used = true;

        return false;
    }

    public function isError(): bool
    {
        $this->used = true;

        return true;
    }

    public function isOk(): bool
    {
        $this->used = true;

        return false;
    }

    public function message(): string
    {
        $this->used = true;

        return $this->exception->getMessage();
    }

    /**
     * @param Closure(E):void $action
     *
     * @return $this
     */
    public function onFailure(Closure $action): static
    {
        $this->used = true;

        $action($this->exception);

        return $this;
    }

    /**
     * @param Closure(T):void $action
     *
     * @return $this
     */
    public function onSuccess(Closure $action): static
    {
        $this->used = true;

        return $this;
    }

    /**
     * @template U
     *
     * @param U|Closure():U $value
     *
     * @return U
     */
    public function or(mixed $value): mixed
    {
        $this->used = true;

        if ($value instanceof Closure) {
            return $value();
        }

        return $value;
    }

    public function orDie(int|string $status = null): never
    {
        $this->used = true;

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
     * @return IResult<U, F>
     */
    public function orElse(IResult|Closure $result): IResult
    {
        $this->used = true;

        if ($result instanceof Closure) {
            return $result();
        }

        return $result;
    }

    /**
     * @throws E
     */
    public function orFail(): never
    {
        $this->used = true;

        throw $this->exception;
    }

    /**
     * @return false
     */
    public function orFalse(): bool
    {
        $this->used = true;

        return false;
    }

    public function orNull(): mixed
    {
        $this->used = true;

        return null;
    }

    /**
     * @template F of Throwable
     *
     * @param F|Closure(Throwable):F $exception
     *
     * @throws F
     */
    public function orThrow(Throwable|Closure $exception): never
    {
        $this->used = true;

        if ($exception instanceof Closure) {
            throw $exception($this->exception);
        }

        throw $exception;
    }

    public function value(): mixed
    {
        $this->used = true;

        return null;
    }
}
