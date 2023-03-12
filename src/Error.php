<?php

declare(strict_types=1);

namespace Hereldar\Results;

use Closure;
use Hereldar\Results\Exceptions\UnusedResult;
use Hereldar\Results\Interfaces\IResult;
use RuntimeException;
use Throwable;

/**
 * @template T
 * @template E of Throwable
 *
 * @implements IResult<T, E>
 */
final class Error implements IResult
{
    private bool $used = false;
    private readonly Backtrace $trace;

    /**
     * @param E $exception
     */
    private function __construct(
        private readonly Throwable $exception,
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
     * @return self<null, RuntimeException>
     *
     * @psalm-suppress MixedReturnTypeCoercion
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
     * @return self<null, F>
     *
     * @psalm-suppress MixedReturnTypeCoercion
     */
    public static function withException(Throwable $exception): self
    {
        return new self($exception);
    }

    /**
     * @return self<null, RuntimeException>
     *
     * @psalm-suppress MixedReturnTypeCoercion
     */
    public static function withMessage(string $message): self
    {
        return new self(new RuntimeException($message));
    }

    /**
     * @template U
     * @template F of ?Throwable
     *
     * @param IResult<U, F>|Closure(null=):IResult<U, F> $result
     *
     * @return $this
     *
     * @phpstan-ignore-next-line
     */
    public function andThen(IResult|Closure $result): self
    {
        $this->used = true;

        return $this;
    }

    /**
     * @return E
     */
    public function exception(): Throwable
    {
        $this->used = true;

        return $this->exception;
    }

    /**
     * @return true
     */
    public function hasException(): bool
    {
        $this->used = true;

        return true;
    }

    public function hasMessage(): bool
    {
        $this->used = true;

        return ($this->exception->getMessage() !== '');
    }

    /**
     * @return false
     */
    public function hasValue(): bool
    {
        $this->used = true;

        return false;
    }

    /**
     * @return true
     */
    public function isError(): bool
    {
        $this->used = true;

        return true;
    }

    /**
     * @return false
     */
    public function isOk(): bool
    {
        $this->used = true;

        return false;
    }

    public function message(): string
    {
        $this->used = true;

        return $this->exception->getMessage();
    }

    /**
     * @param Closure(E=):void $action
     *
     * @return $this
     */
    public function onFailure(Closure $action): static
    {
        $this->used = true;

        $action($this->exception);

        return $this;
    }

    /**
     * @param Closure(null=):void $action
     *
     * @return $this
     */
    public function onSuccess(Closure $action): static
    {
        $this->used = true;

        return $this;
    }

    /**
     * @template U
     *
     * @param U|Closure(null=):U $value
     *
     * @return U
     */
    public function or(mixed $value): mixed
    {
        $this->used = true;

        if ($value instanceof Closure) {
            /** @var U */
            return $value(null);
        }

        return $value;
    }

    public function orDie(int|string $status = null): never
    {
        $this->used = true;

        if (isset($status)) {
            exit($status);
        }

        exit;
    }

    /**
     * @template U
     * @template F of ?Throwable
     *
     * @param IResult<U, F>|Closure(null=):IResult<U, F> $result
     *
     * @return IResult<U, F>
     */
    public function orElse(IResult|Closure $result): IResult
    {
        $this->used = true;

        if ($result instanceof Closure) {
            /** @var IResult<U, F> */
            return $result(null);
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
        $this->used = true;

        throw $this->exception;
    }

    /**
     * @return false
     */
    public function orFalse(): bool
    {
        $this->used = true;

        return false;
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
     * @template F of Throwable
     *
     * @param F|Closure(E=):F $exception
     *
     * @throws F
     *
     * @psalm-suppress UndefinedDocblockClass
     */
    public function orThrow(Throwable|Closure $exception): never
    {
        $this->used = true;

        if ($exception instanceof Closure) {
            throw $exception($this->exception);
        }

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
