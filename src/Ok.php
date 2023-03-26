<?php

declare(strict_types=1);

namespace Hereldar\Results;

use Closure;
use Hereldar\Results\Exceptions\UnusedResult;
use Hereldar\Results\Interfaces\IResult;
use Throwable;

/**
 * @template T
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
     * @return self<null>
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
     * @return self<U>
     */
    public static function withValue(mixed $value): self
    {
        return new self($value);
    }

    /**
     * @template U
     * @template F of Throwable
     *
     * @param Ok<U>|Error<F>|Closure(T):(Ok<U>|Error<F>) $result
     *
     * @return Ok<U>|Error<F>
     * @phpstan-return ($result is Ok ? Ok<U> : ($result is Error ? Error<F> : Ok<U>|Error<F>))
     * @psalm-return ($result is Ok ? Ok<U> : ($result is Error ? Error<F> : Ok<U>|Error<F>))
     *
     * @psalm-suppress MoreSpecificImplementedParamType
     * @psalm-suppress TypeDoesNotContainType
     * @psalm-suppress MixedInferredReturnType
     * @psalm-suppress MixedReturnStatement
     * @psalm-suppress InvalidReturnStatement
     */
    public function andThen(Ok|Error|Closure $result): Ok|Error
    {
        $this->used = true;

        if ($result instanceof Closure) {
            return $result($this->value);
        }

        return $result;
    }

    /**
     * @return null
     *
     * @psalm-suppress  InvalidReturnStatement
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
     * @param Closure(Throwable):void $action
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
     * @param U|Closure(T):U $value
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
     * @param Ok<U>|Error<F>|Closure():(Ok<U>|Error<F>) $result
     *
     * @return $this
     *
     * @psalm-suppress MoreSpecificImplementedParamType
     */
    public function orElse(Ok|Error|Closure $result): static
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
     * @param F|Closure(Throwable):F $exception
     *
     * @return T
     *
     * @psalm-suppress MoreSpecificImplementedParamType
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
