<?php

declare(strict_types=1);

namespace Hereldar\Results;

use Closure;
use Hereldar\Results\Exceptions\UnusedResult;
use Hereldar\Results\Interfaces\IResult;
use Throwable;

/**
 * @template T
 *
 * @implements IResult<T, null>
 */
class Ok implements IResult
{
    protected bool $used = false;
    private readonly string $trace;

    /**
     * @param T $value
     */
    public function __construct(
        protected readonly mixed $value = null,
    ) {
        ob_start();
        debug_print_backtrace(limit: 5);
        $this->trace = ob_get_contents();
        ob_end_clean();
    }

    public function __destruct()
    {
        if (!$this->used) {
            throw new UnusedResult($this, $this->trace);
        }
    }

    /**
     * @return static<null>
     */
    public static function empty(): static
    {
        return new static();
    }

    /**
     * @template U
     *
     * @param U $value
     *
     * @return static<U>
     */
    public static function withValue(mixed $value): static
    {
        return new static($value);
    }

    /**
     * @template U
     * @template F of Throwable
     *
     * @param IResult<U, F>|Closure(T):IResult<U, F> $result
     *
     * @return IResult<U, F>
     */
    final public function andThen(IResult|Closure $result): IResult
    {
        $this->used = true;

        if ($result instanceof Closure) {
            return $result($this->value());
        }

        return $result;
    }

    final public function exception(): ?Throwable
    {
        $this->used = true;

        return null;
    }

    final public function hasMessage(): bool
    {
        $this->used = true;

        return false;
    }

    final public function hasException(): bool
    {
        $this->used = true;

        return false;
    }

    final public function hasValue(): bool
    {
        $this->used = true;

        return ($this->value !== null);
    }

    final public function isError(): bool
    {
        $this->used = true;

        return false;
    }

    final public function isOk(): bool
    {
        $this->used = true;

        return true;
    }

    final public function message(): string
    {
        $this->used = true;

        return '';
    }

    /**
     * @template U
     *
     * @param U|Closure():U $value
     *
     * @return T
     */
    final public function or(mixed $value): mixed
    {
        $this->used = true;

        return $this->value;
    }

    /**
     * @return T
     */
    final public function orDie(int|string $status = null): mixed
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
    final public function orElse(IResult|Closure $result): static
    {
        $this->used = true;

        return $this;
    }

    /**
     * @return T
     */
    final public function orFail(): mixed
    {
        $this->used = true;

        return $this->value;
    }

    /**
     * @return T
     */
    final public function orNull(): mixed
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
    final public function orThrow(Throwable $exception): mixed
    {
        $this->used = true;

        return $this->value;
    }

    /**
     * @return T
     */
    final public function value(): mixed
    {
        $this->used = true;

        return $this->value;
    }
}
