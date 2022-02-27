<?php

declare(strict_types=1);

namespace Hereldar\Results;

use Closure;
use Hereldar\Results\Interfaces\IResult;

abstract class AbstractResult implements IResult
{
    protected string $message = '';
    protected mixed $value = null;

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

    abstract public function orFail(): mixed;

    public function orNull(): mixed
    {
        if ($this->isOk()) {
            return $this->value;
        }

        return null;
    }

    public function value(): mixed
    {
        return $this->value;
    }
}
