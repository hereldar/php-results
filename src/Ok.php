<?php

declare(strict_types=1);

namespace Hereldar\Results;

use Closure;
use Hereldar\Results\Exceptions\UnusedResult;
use Hereldar\Results\Interfaces\IResult;
use Throwable;

/**
 * @template T
 * @template E of ?Throwable
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
    private function __construct(
        private readonly mixed $value,
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
     * @return self<null, null>
     *
     * @psalm-suppress MixedReturnTypeCoercion
     */
    public static function empty(): self
    {
        /* @phpstan-ignore-next-line */
        return new self(null);
    }

    /**
     * @template U
     *
     * @param U $value
     *
     * @return self<U, null>
     *
     * @psalm-suppress MixedReturnTypeCoercion
     */
    public static function withValue(mixed $value): self
    {
        /* @phpstan-ignore-next-line */
        return new self($value);
    }

    /**
     * @template U
     * @template F of ?Throwable
     *
     * @param IResult<U, F>|Closure(T=):IResult<U, F> $result
     *
     * @return IResult<U, F>
     */
    public function andThen(IResult|Closure $result): IResult
    {
        $this->used = true;

        if ($result instanceof Closure) {
            /** @var IResult<U, F> */
            return $result($this->value);
        }

        return $result;
    }

    /**
     * @return null
     */
    public function exception(): ?Throwable
    {
        $this->used = true;

        return null;
    }

    /**
     * @return false
     */
    public function hasException(): bool
    {
        $this->used = true;

        return false;
    }

    /**
     * @return false
     */
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

    /**
     * @return false
     */
    public function isError(): bool
    {
        $this->used = true;

        return false;
    }

    /**
     * @return true
     */
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
     * @param Closure(null=):void $action
     *
     * @return $this
     */
    public function onFailure(Closure $action): static
    {
        $this->used = true;

        return $this;
    }

    /**
     * @param Closure(T=):void $action
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
     * @param U|Closure(T=):U $value
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
     * @template F of ?Throwable
     *
     * @param IResult<U, F>|Closure(T=):IResult<U, F> $result
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
     * @param F|Closure(null=):F $exception
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
