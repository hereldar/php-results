<?php

declare(strict_types=1);

namespace Hereldar\Results;

use Closure;
use Hereldar\Results\Exceptions\UnusedResult;
use Hereldar\Results\Interfaces\IResult;
use Throwable;

/**
 * @template T
 * @template E of Throwable
 *
 * @implements IResult<T, E>
 */
final class Ok implements IResult
{
    private bool $used = false;
    private readonly Backtrace $trace;

    /**
     * @param T $value
     */
    public function __construct(
        private readonly mixed $value = null,
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
     * @return self<null, E>
     */
    public static function empty(): self
    {
        return new self(null);
    }

    /**
     * @template U
     *
     * @param U $value
     *
     * @return self<U, E>
     */
    public static function withValue(mixed $value): self
    {
        return new self($value);
    }

    /**
     * @template U
     * @template F of Throwable
     *
     * @param IResult<U, F>|Closure(T|null):IResult<U, F> $result
     *
     * @return IResult<U, F>
     */
    public function andThen(IResult|Closure $result): IResult
    {
        $this->used = true;

        if ($result instanceof Closure) {
            return $result($this->value);
        }

        return $result;
    }

    public function exception(): ?Throwable
    {
        $this->used = true;

        return null;
    }

    public function hasException(): bool
    {
        $this->used = true;

        return false;
    }

    public function hasMessage(): bool
    {
        $this->used = true;

        return false;
    }

    public function hasValue(): bool
    {
        $this->used = true;

        return ($this->value !== null);
    }

    public function isError(): bool
    {
        $this->used = true;

        return false;
    }

    public function isOk(): bool
    {
        $this->used = true;

        return true;
    }

    public function message(): string
    {
        $this->used = true;

        return '';
    }

    /**
     * @param Closure(E):void $action
     *
     * @return $this
     */
    public function onFailure(Closure $action): static
    {
        $this->used = true;

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

        $action($this->value);

        return $this;
    }

    /**
     * @template U
     *
     * @param U|Closure():U $value
     *
     * @return T
     */
    public function or(mixed $value): mixed
    {
        $this->used = true;

        return $this->value;
    }

    /**
     * @return T
     */
    public function orDie(int|string $status = null): mixed
    {
        $this->used = true;

        return $this->value;
    }

    /**
     * @template U
     * @template F of Throwable
     *
     * @param IResult<U, F>|Closure():IResult<U, F> $result
     *
     * @return $this
     */
    public function orElse(IResult|Closure $result): self
    {
        $this->used = true;

        return $this;
    }

    /**
     * @return T
     */
    public function orFail(): mixed
    {
        $this->used = true;

        return $this->value;
    }

    /**
     * @return T
     */
    public function orFalse(): mixed
    {
        $this->used = true;

        return $this->value;
    }

    /**
     * @return T
     */
    public function orNull(): mixed
    {
        $this->used = true;

        return $this->value;
    }

    /**
     * @template F of Throwable
     *
     * @param F|Closure():F $exception
     *
     * @return T
     */
    public function orThrow(Throwable|Closure $exception): mixed
    {
        $this->used = true;

        return $this->value;
    }

    /**
     * @return T
     */
    public function value(): mixed
    {
        $this->used = true;

        return $this->value;
    }
}
