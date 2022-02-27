<?php

declare(strict_types=1);

namespace Hereldar\Results;

use Closure;
use Hereldar\Results\Interfaces\IResult;
use RuntimeException;

class Error extends RuntimeException implements IResult
{
    public function __construct(
        string $message = ''
    ) {
        parent::__construct($message);
    }

    public static function empty(): static
    {
        return new static();
    }

    public function andThen(IResult|Closure $default): IResult
    {
        return $this;
    }

    public function hasMessage(): bool
    {
        return ($this->message !== '');
    }

    public function hasValue(): bool
    {
        return false;
    }

    public final function isError(): bool
    {
        return true;
    }

    public final function isOk(): bool
    {
        return false;
    }

    public final function message(): string
    {
        return $this->message;
    }

    public function or(mixed $default): mixed
    {
        if ($default instanceof Closure) {
            return $default();
        }

        return $default;
    }

    public function orDie(int|string $status = null): never
    {
        if (isset($status)) {
            exit($status);
        } else {
            exit;
        }
    }

    public function orElse(IResult|Closure $default): IResult
    {
        if ($default instanceof Closure) {
            return $default();
        }

        return $default;
    }

    /**
     * @throws static
     */
    public function orFail(): mixed
    {
        throw $this;
    }

    public function orNull(): mixed
    {
        return null;
    }

    public function value(): mixed
    {
        return null;
    }
}
