<?php

declare(strict_types=1);

namespace Hereldar\Results;

use Closure;
use Hereldar\Results\Interfaces\Resultlike;
use Throwable;

/**
 * Contains the success value.
 *
 * @template-covariant T
 */
final class Ok implements Resultlike
{
    /** @var self<null>|null */
    private static ?Ok $empty = null;

    /**
     * @param T $value
     */
    private function __construct(
        private readonly mixed $value,
    ) {
    }

    /**
     * Returns a successful result containing no value (`null`).
     *
     * @return self<null>
     */
    public static function empty(): self
    {
        return self::$empty ??= new self(null);
    }

    /**
     * Makes a new `Ok` with the given `value`.
     *
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
        return null;
    }

    /**
     * @return false
     */
    public function hasException(): bool
    {
        return false;
    }

    /**
     * @return false
     */
    public function hasMessage(): bool
    {
        return false;
    }

    public function hasValue(): bool
    {
        return ($this->value !== null);
    }

    /**
     * @return false
     */
    public function isError(): bool
    {
        return false;
    }

    /**
     * @return true
     */
    public function isOk(): bool
    {
        return true;
    }

    public function message(): string
    {
        return '';
    }

    /**
     * @param Closure(Throwable):void $action
     *
     * @return $this
     */
    public function onFailure(Closure $action): static
    {
        return $this;
    }

    /**
     * @param Closure(T):void $action
     *
     * @return $this
     *
     * @psalm-suppress InvalidTemplateParam
     */
    public function onSuccess(Closure $action): static
    {
        $action($this->value);

        return $this;
    }

    /**
     * @template U
     *
     * @param U|Closure():U $value
     *
     * @return T
     */
    public function or(mixed $value): mixed
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
    public function orFalse(): mixed
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
