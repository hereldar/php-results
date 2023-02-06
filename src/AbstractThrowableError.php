<?php

declare(strict_types=1);

namespace Hereldar\Results;

use Closure;
use Exception;
use Hereldar\Results\Exceptions\UnusedResult;
use Hereldar\Results\Interfaces\IResult;
use Throwable;

/**
 * @implements IResult<null, AbstractThrowableError>
 */
abstract class AbstractThrowableError extends Exception implements IResult
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

    /**
     * @template U
     * @template F of ?Throwable
     *
     * @param IResult<U, F>|Closure(null=):IResult<U, F> $result
     *
     * @return $this
     */
    final public function andThen(IResult|Closure $result): static
    {
        $this->used = true;

        return $this;
    }

    /**
     * @return $this
     */
    final public function exception(): static
    {
        $this->used = true;

        return $this;
    }

    /**
     * @return true
     */
    final public function hasException(): bool
    {
        $this->used = true;

        return true;
    }

    final public function hasMessage(): bool
    {
        $this->used = true;

        return ($this->message !== '');
    }

    /**
     * @return false
     */
    final public function hasValue(): bool
    {
        $this->used = true;

        return false;
    }

    /**
     * @return true
     */
    final public function isError(): bool
    {
        $this->used = true;

        return true;
    }

    /**
     * @return false
     */
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
     * @param Closure(static=):void $action
     *
     * @return $this
     */
    public function onFailure(Closure $action): static
    {
        $this->used = true;

        $action($this);

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
    final public function or(mixed $value): mixed
    {
        $this->used = true;

        if ($value instanceof Closure) {
            /** @var U */
            return $value(null);
        }

        return $value;
    }

    final public function orDie(int|string $status = null): never
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
    final public function orElse(IResult|Closure $result): IResult
    {
        $this->used = true;

        if ($result instanceof Closure) {
            /** @var IResult<U, F> */
            return $result(null);
        }

        return $result;
    }

    /**
     * @throws static
     */
    final public function orFail(): never
    {
        $this->used = true;

        throw $this;
    }

    /**
     * @return false
     */
    final public function orFalse(): bool
    {
        $this->used = true;

        return false;
    }

    /**
     * @return null
     */
    final public function orNull(): mixed
    {
        $this->used = true;

        return null;
    }

    /**
     * @template F of Throwable
     *
     * @param F|Closure(static=):F $exception
     *
     * @throws F
     */
    final public function orThrow(Throwable|Closure $exception): never
    {
        $this->used = true;

        if ($exception instanceof Closure) {
            throw $exception($this);
        }

        throw $exception;
    }

    /**
     * @return null
     */
    final public function value(): mixed
    {
        $this->used = true;

        return null;
    }
}
