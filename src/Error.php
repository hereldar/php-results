<?php

declare(strict_types=1);

namespace Hereldar\Results;

use Closure;
use Hereldar\Results\Exceptions\UnusedResult;
use Hereldar\Results\Interfaces\IResult;
use RuntimeException;

/**
 * @implements IResult<null>
 */
class Error extends RuntimeException implements IResult
{
    protected bool $used = false;

    public function __construct(
        string $message = ''
    ) {
        parent::__construct($message);
    }

    public function __destruct()
    {
        if (!$this->used) {
            $trace = $this->getTraceAsString();
            $lines = array_slice(explode("\n", $trace), 0, 5);

            throw new UnusedResult($this, implode("\n", $lines));
        }
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
        $this->used = true;

        return $this;
    }

    public function hasMessage(): bool
    {
        $this->used = true;

        return ($this->message !== '');
    }

    public function hasValue(): bool
    {
        $this->used = true;

        return false;
    }

    final public function isError(): bool
    {
        $this->used = true;

        return true;
    }

    final public function isOk(): bool
    {
        $this->used = true;

        return false;
    }

    final public function message(): string
    {
        $this->used = true;

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
        $this->used = true;

        if ($default instanceof Closure) {
            return $default();
        }

        return $default;
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
     * @template T2
     *
     * @param IResult<T2>|Closure():IResult<T2> $default
     *
     * @return IResult<T2>
     */
    public function orElse(IResult|Closure $default): IResult
    {
        $this->used = true;

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
        $this->used = true;

        throw $this;
    }

    /**
     * @return null
     */
    public function orNull(): mixed
    {
        $this->used = true;

        return null;
    }

    /**
     * @template TException of RuntimeException
     *
     * @param TException $exception
     *
     * @throws TException
     */
    public function orThrow(RuntimeException $exception): never
    {
        $this->used = true;

        throw $exception;
    }

    /**
     * @return null
     */
    public function value(): mixed
    {
        $this->used = true;

        return null;
    }
}
