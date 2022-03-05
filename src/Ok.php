<?php

declare(strict_types=1);

namespace Hereldar\Results;

use Closure;
use Hereldar\Results\Interfaces\IResult;

class Ok implements IResult
{
    public function __construct(
        protected readonly mixed $value = null,
        protected readonly string $message = '',
    ) {
    }

    public static function empty(): static
    {
        return new static();
    }

    public static function of(mixed $value): static
    {
        return new static($value);
    }

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

    public function or(mixed $default): mixed
    {
        return $this->value;
    }

    public function orDie(int|string $status = null): mixed
    {
        return $this->value;
    }

    public function orElse(IResult|Closure $default): static
    {
        return $this;
    }

    public function orFail(): mixed
    {
        return $this->value;
    }

    public function orNull(): mixed
    {
        return $this->value;
    }

    public function value(): mixed
    {
        return $this->value;
    }
}
