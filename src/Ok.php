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
class Ok implements IResult
{
    /**
     * @param T $value
     */
    public function __construct(
        protected readonly mixed $value = null,
        protected readonly string $message = '',
    ) {
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

    final public function isError(): bool
    {
        return false;
    }

    final public function isOk(): bool
    {
        return true;
    }

    public function message(): string
    {
        return $this->message;
    }

    /**
     * @return T
     */
    public function or(mixed $default): mixed
    {
        return $this->value;
    }

    /**
     * @return T
     */
    public function orDie(int|string $status = null): mixed
    {
        return $this->value;
    }

    /**
     * @return $this
     */
    public function orElse(IResult|Closure $default): static
    {
        return $this;
    }

    /**
     * @return T
     */
    public function orFail(): mixed
    {
        return $this->value;
    }

    /**
     * @return T
     */
    public function orNull(): mixed
    {
        return $this->value;
    }

    /**
     * @return T
     */
    public function orThrow(RuntimeException $exception): mixed
    {
        return $this->value;
    }

    /**
     * @return T
     */
    public function value(): mixed
    {
        return $this->value;
    }
}
