<?php

declare(strict_types=1);

namespace Hereldar\Results;

use Closure;
use Hereldar\Results\Exceptions\UnusedResult;
use Hereldar\Results\Interfaces\IResult;
use RuntimeException;

/**
 * @template T
 *
 * @implements IResult<T>
 */
class Ok implements IResult
{
    protected bool $used = false;
    private string $trace;

    /**
     * @param T $value
     */
    public function __construct(
        protected readonly mixed $value = null,
        protected readonly string $message = '',
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
     * @param T $value
     *
     * @return static<T>
     */
    public static function of(mixed $value): static
    {
        return new static($value);
    }

    /**
     * @template T2
     *
     * @param IResult<T2>|Closure(T):IResult<T2> $default
     *
     * @return IResult<T2>
     */
    public function andThen(IResult|Closure $default): IResult
    {
        $this->used = true;

        if ($default instanceof Closure) {
            return $default($this->value());
        }

        return $default;
    }

    public function hasMessage(): bool
    {
        $this->used = true;

        return ($this->message !== '');
    }

    public function hasValue(): bool
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

    public function message(): string
    {
        $this->used = true;

        return $this->message;
    }

    /**
     * @return T
     */
    public function or(mixed $default): mixed
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
     * @return $this
     */
    public function orElse(IResult|Closure $default): static
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
    public function orNull(): mixed
    {
        $this->used = true;

        return $this->value;
    }

    /**
     * @return T
     */
    public function orThrow(RuntimeException $exception): mixed
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
