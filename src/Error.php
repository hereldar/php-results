<?php

declare(strict_types=1);

namespace Hereldar\Results;

use Closure;
use Hereldar\Results\Interfaces\Resultlike;
use RuntimeException;
use Throwable;

/**
 * Contains the error value.
 *
 * Instances of this class are immutable and not affected by any
 * method calls.
 *
 * @template-covariant E
 */
final class Error implements Resultlike
{
    /** @var self<null>|null */
    private static ?Error $empty = null;

    /**
     * @param E $value
     */
    private function __construct(
        private readonly mixed $value,
    ) {}

    /**
     * Makes a new `Error` with the given `value`.
     *
     * @template F
     *
     * @param F $value
     *
     * @return self<F>
     */
    public static function of(mixed $value): self
    {
        return new self($value);
    }

    /**
     * Returns an `Error` containing no value (`null`).
     *
     * @return self<null>
     */
    public static function empty(): self
    {
        return self::$empty ??= new self(null);
    }

    /**
     * @template U
     * @template F
     *
     * @param Ok<U>|Error<F>|Closure(mixed):(Ok<U>|Error<F>) $result
     *
     * @return $this
     *
     * @psalm-suppress MoreSpecificImplementedParamType
     */
    public function andThen(Ok|Error|Closure $result): static
    {
        return $this;
    }

    public function hasValue(): bool
    {
        return ($this->value !== null);
    }

    /**
     * @return true
     */
    public function isError(): bool
    {
        return true;
    }

    /**
     * @return false
     */
    public function isOk(): bool
    {
        return false;
    }

    /**
     * @param Closure(E):mixed $action
     *
     * @return $this
     *
     * @psalm-suppress InvalidTemplateParam
     */
    public function onFailure(Closure $action): static
    {
        $action($this->value);

        return $this;
    }

    /**
     * @param Closure(mixed):mixed $action
     *
     * @return $this
     */
    public function onSuccess(Closure $action): static
    {
        return $this;
    }

    /**
     * @template U
     *
     * @param U|Closure():U $value
     *
     * @return U
     *
     * @psalm-suppress MixedInferredReturnType
     * @psalm-suppress MixedReturnStatement
     */
    public function or(mixed $value): mixed
    {
        if ($value instanceof Closure) {
            return $value();
        }

        return $value;
    }

    public function orDie(int|string $status = null): never
    {
        if ($status !== null) {
            exit($status);
        }

        exit;
    }

    /**
     * @template U
     * @template F
     *
     * @param Ok<U>|Error<F>|Closure():(Ok<U>|Error<F>) $result
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
    public function orElse(Ok|Error|Closure $result): Ok|Error
    {
        if ($result instanceof Closure) {
            return $result();
        }

        return $result;
    }

    /**
     * @throws E|RuntimeException
     * @phpstan-throws (E is Throwable ? E : RuntimeException)
     *
     * @psalm-suppress UndefinedDocblockClass
     */
    public function orFail(): never
    {
        if ($this->value instanceof Throwable) {
            throw $this->value;
        }

        throw new RuntimeException();
    }

    /**
     * @return false
     */
    public function orFalse(): bool
    {
        return false;
    }

    /**
     * @return null
     *
     * @psalm-suppress  InvalidReturnStatement
     */
    public function orNull(): mixed
    {
        return null;
    }

    /**
     * @template F of Throwable
     *
     * @param F|Closure(E):F $exception
     *
     * @throws F
     *
     * @psalm-suppress MoreSpecificImplementedParamType
     * @psalm-suppress UndefinedDocblockClass
     * @psalm-suppress InvalidTemplateParam
     */
    public function orThrow(Throwable|Closure $exception): never
    {
        if ($exception instanceof Closure) {
            throw $exception($this->value);
        }

        throw $exception;
    }

    /**
     * @return E
     */
    public function value(): mixed
    {
        return $this->value;
    }
}
