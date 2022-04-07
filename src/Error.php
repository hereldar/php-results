<?php

declare(strict_types=1);

namespace Hereldar\Results;

use Closure;
use Hereldar\Results\Exceptions\UnusedResult;
use Hereldar\Results\Interfaces\IResult;
use RuntimeException;
use Throwable;

/**
 * @template E of Throwable
 *
 * @implements IResult<null, E>
 *
 * @psalm-consistent-constructor
 * @psalm-consistent-templates
 */
class Error implements IResult
{
    protected bool $used = false;

    private readonly string $trace;

    /**
     * @param E $exception
     */
    public function __construct(
        private readonly Throwable $exception
    ) {
        ob_start();
        debug_print_backtrace(limit: 5);
        $this->trace = ob_get_contents();
        ob_end_clean();
    }

    public function __destruct()
    {
        if (!$this->used) {
            throw new UnusedResult($this, $this->trace);
        }
    }

    /**
     * @return static<RuntimeException>
     */
    public static function empty(): static
    {
        return new static(new RuntimeException());
    }

    /**
     * @template F of Throwable
     *
     * @param F $exception
     *
     * @return static<F>
     */
    public static function fromException(Throwable $exception): static
    {
        return new static($exception);
    }

    /**
     * @return static<RuntimeException>
     */
    public static function withMessage(string $message): static
    {
        return new static(new RuntimeException($message));
    }

    /**
     * @template U
     * @template F of Throwable|null
     *
     * @param IResult<U, F>|Closure(null):IResult<U, F> $result
     *
     * @return $this
     */
    final public function andThen(IResult|Closure $result): static
    {
        $this->used = true;

        return $this;
    }

    /**
     * @return E
     */
    final public function exception(): Throwable
    {
        $this->used = true;

        return $this->exception;
    }

    final public function hasException(): bool
    {
        $this->used = true;

        return true;
    }

    final public function hasMessage(): bool
    {
        $this->used = true;

        return ($this->exception->getMessage() !== '');
    }

    final public function hasValue(): bool
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

        return $this->exception->getMessage();
    }

    /**
     * @template U
     *
     * @param U|Closure():U $value
     *
     * @return U
     */
    final public function or(mixed $value): mixed
    {
        $this->used = true;

        if ($value instanceof Closure) {
            return $value();
        }

        return $value;
    }

    final public function orDie(int|string $status = null): never
    {
        $this->used = true;

        if (isset($status)) {
            exit($status);
        } else {
            exit;
        }
    }

    /**
     * @template U
     * @template F of Throwable|null
     *
     * @param IResult<U, F>|Closure():IResult<U, F> $result
     *
     * @return IResult<U, F>
     */
    final public function orElse(IResult|Closure $result): IResult
    {
        $this->used = true;

        if ($result instanceof Closure) {
            return $result();
        }

        return $result;
    }

    /**
     * @throws E
     */
    final public function orFail(): never
    {
        $this->used = true;

        throw $this->exception;
    }

    final public function orNull(): mixed
    {
        $this->used = true;

        return null;
    }

    /**
     * @template F of Throwable
     *
     * @param F|Closure():F $exception
     *
     * @throws F
     */
    final public function orThrow(Throwable|Closure $exception): never
    {
        $this->used = true;

        if ($exception instanceof Closure) {
            throw $exception();
        }

        throw $exception;
    }

    final public function value(): mixed
    {
        $this->used = true;

        return null;
    }
}
