<?php

declare(strict_types=1);

namespace Hereldar\Results;

use Closure;
use Hereldar\Results\Exceptions\UnusedResult;
use Hereldar\Results\Interfaces\IResult;
use Throwable;

/**
 * @template T
 * @template E of Throwable|null
 *
 * @implements IResult<T, E>
 */
abstract class AbstractResult implements IResult
{
    protected bool $used = false;
    private readonly Backtrace $trace;

    /**
     * @param T $value
     * @param E $exception
     */
    public function __construct(
        protected readonly mixed $value = null,
        protected readonly ?Throwable $exception = null
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
     * @template U
     * @template F of ?Throwable
     *
     * @param IResult<U, F>|Closure(T=):IResult<U, F> $result
     *
     * @return $this|IResult<U, F>
     */
    public function andThen(IResult|Closure $result): static|IResult
    {
        if ($this->exception !== null) {
            return $this;
        }

        if ($result instanceof Closure) {
            /** @var IResult<U, F> */
            return $result($this->value);
        }

        return $result;
    }

    /**
     * @return E
     */
    final public function exception(): ?Throwable
    {
        $this->used = true;

        return $this->exception;
    }

    /**
     * @phpstan-assert-if-true Throwable $this->exception
     * @phpstan-assert-if-true Throwable $this->exception()
     * @phpstan-assert-if-false null $this->exception
     * @phpstan-assert-if-false null $this->exception()
     *
     * @psalm-assert-if-true Throwable $this->exception
     * @psalm-assert-if-true Throwable $this->exception()
     * @psalm-assert-if-false null $this->exception
     * @psalm-assert-if-false null $this->exception()
     */
    final public function hasException(): bool
    {
        $this->used = true;

        return ($this->exception !== null);
    }

    public function hasMessage(): bool
    {
        return ($this->message() !== '');
    }

    public function hasValue(): bool
    {
        $this->used = true;

        return ($this->value !== null);
    }

    /**
     * @phpstan-assert-if-true Throwable $this->exception
     * @phpstan-assert-if-true Throwable $this->exception()
     * @phpstan-assert-if-false null $this->exception
     * @phpstan-assert-if-false null $this->exception()
     *
     * @psalm-assert-if-true Throwable $this->exception
     * @psalm-assert-if-true Throwable $this->exception()
     * @psalm-assert-if-false null $this->exception
     * @psalm-assert-if-false null $this->exception()
     */
    final public function isError(): bool
    {
        $this->used = true;

        return ($this->exception !== null);
    }

    /**
     * @phpstan-assert-if-true null $this->exception
     * @phpstan-assert-if-true null $this->exception()
     * @phpstan-assert-if-false Throwable $this->exception
     * @phpstan-assert-if-false Throwable $this->exception()
     *
     * @psalm-assert-if-true null $this->exception
     * @psalm-assert-if-true null $this->exception()
     * @psalm-assert-if-false Throwable $this->exception
     * @psalm-assert-if-false Throwable $this->exception()
     */
    final public function isOk(): bool
    {
        $this->used = true;

        return (null === $this->exception);
    }

    public function message(): string
    {
        $this->used = true;

        if (null === $this->exception) {
            return '';
        }

        return $this->exception->getMessage();
    }

    /**
     * @param Closure(E=):void $action
     *
     * @return $this
     */
    public function onFailure(Closure $action): static
    {
        if ($this->exception !== null) {
            $action($this->exception);
        }

        return $this;
    }

    /**
     * @param Closure(T=):void $action
     *
     * @return $this
     */
    public function onSuccess(Closure $action): static
    {
        if (null === $this->exception) {
            $action($this->value);
        }

        return $this;
    }

    /**
     * @template U
     *
     * @param U|Closure(T=):U $value
     *
     * @return T|U
     */
    public function or(mixed $value): mixed
    {
        if (null === $this->exception) {
            return $this->value;
        }

        if ($value instanceof Closure) {
            /** @var U */
            return $value($this->value);
        }

        return $value;
    }

    /**
     * @return T
     */
    public function orDie(int|string $status = null): mixed
    {
        if (null === $this->exception) {
            return $this->value;
        }

        if ($status !== null) {
            exit($status);
        }

        exit;
    }

    /**
     * @template U
     * @template F of ?Throwable
     *
     * @param IResult<U, F>|Closure(T=):IResult<U, F> $result
     *
     * @return $this|IResult<U, F>
     */
    public function orElse(IResult|Closure $result): static|IResult
    {
        if (null === $this->exception) {
            return $this;
        }

        if ($result instanceof Closure) {
            /** @var IResult<U, F> */
            return $result($this->value);
        }

        return $result;
    }

    /**
     * @throws E
     *
     * @return T
     */
    public function orFail(): mixed
    {
        if ($this->exception !== null) {
            throw $this->exception;
        }

        return $this->value;
    }

    /**
     * @return T|false
     */
    public function orFalse(): mixed
    {
        if ($this->exception !== null) {
            return false;
        }

        return $this->value;
    }

    /**
     * @return T|null
     */
    public function orNull(): mixed
    {
        if ($this->exception !== null) {
            return null;
        }

        return $this->value;
    }

    /**
     * @template F of Throwable
     *
     * @param F|Closure(E=):F $exception
     *
     * @throws F
     *
     * @return T
     */
    public function orThrow(Throwable|Closure $exception): mixed
    {
        if (null === $this->exception) {
            return $this->value;
        }

        if ($exception instanceof Closure) {
            throw $exception($this->exception);
        }

        throw $exception;
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
