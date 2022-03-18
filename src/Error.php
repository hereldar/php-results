<?php

declare(strict_types=1);

namespace Hereldar\Results;

use Closure;
use Hereldar\Results\Interfaces\IResult;
use RuntimeException;

/**
 * @implements IResult<null>
 */
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

    /**
     * @return $this
     */
    public function andThen(IResult|Closure $default): static
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

    final public function isError(): bool
    {
        return true;
    }

    final public function isOk(): bool
    {
        return false;
    }

    final public function message(): string
    {
        return $this->message;
    }

    /**
     * @template T2
     *
     * @param T2|Closure():T2 $default
     *
     * @return T2
     */
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

    /**
     * @template T2
     *
     * @param IResult<T2>|Closure():IResult<T2> $default
     *
     * @return IResult<T2>
     */
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
    public function orFail(): never
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
