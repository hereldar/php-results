<?php

declare(strict_types=1);

namespace Hereldar\Results;

use Closure;
use Hereldar\Results\Interfaces\IResult;
use RuntimeException;
use Throwable;

/**
 * @template E of Throwable
 */
final class Error implements IResult
{
    /**
     * @param E $exception
     */
    private function __construct(
        private readonly Throwable $exception,
    ) {
    }

    /**
     * @return self<RuntimeException>
     */
    public static function empty(): self
    {
        return new self(new RuntimeException());
    }

    /**
     * @template F of Throwable
     *
     * @param F $exception
     *
     * @return self<F>
     */
    public static function withException(Throwable $exception): self
    {
        return new self($exception);
    }

    /**
     * @return self<RuntimeException>
     */
    public static function withMessage(string $message): self
    {
        return new self(new RuntimeException($message));
    }

    /**
     * @template U
     * @template F of Throwable
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

    /**
     * @return E
     */
    public function exception(): Throwable
    {
        return $this->exception;
    }

    /**
     * @return true
     */
    public function hasException(): bool
    {
        return true;
    }

    public function hasMessage(): bool
    {
        return ($this->exception->getMessage() !== '');
    }

    /**
     * @return false
     */
    public function hasValue(): bool
    {
        return false;
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

    public function message(): string
    {
        return $this->exception->getMessage();
    }

    /**
     * @param Closure(E):void $action
     *
     * @return $this
     */
    public function onFailure(Closure $action): static
    {
        $action($this->exception);

        return $this;
    }

    /**
     * @param Closure(mixed):void $action
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
        if (isset($status)) {
            exit($status);
        }

        exit;
    }

    /**
     * @template U
     * @template F of Throwable
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
     * @throws E
     *
     * @psalm-suppress UndefinedDocblockClass
     */
    public function orFail(): never
    {
        throw $this->exception;
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
     */
    public function orThrow(Throwable|Closure $exception): never
    {
        if ($exception instanceof Closure) {
            throw $exception($this->exception);
        }

        throw $exception;
    }

    /**
     * @return null
     * 
     * @psalm-suppress  InvalidReturnStatement
     */
    public function value(): mixed
    {
        return null;
    }
}
