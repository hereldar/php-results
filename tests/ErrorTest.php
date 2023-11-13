<?php

declare(strict_types=1);

namespace Hereldar\Results\Tests;

use Exception;
use Hereldar\Results\Error;
use Hereldar\Results\Ok;
use LogicException;
use RuntimeException;
use Throwable;
use UnexpectedValueException;

/**
 * @psalm-suppress MissingConstructor
 * @psalm-suppress PropertyNotSetInConstructor
 */
final class ErrorTest extends TestCase
{
    /** @var Error<null> */
    private Error $emptyError;

    /** @var Error<LogicException> */
    private Error $errorWithException;

    /** @var Error<string> */
    private Error $errorWithMessage;

    /** @var Ok<null> */
    private Ok $ok;

    protected function setUp(): void
    {
        parent::setUp();

        $this->emptyError = Error::empty();
        $this->errorWithException = Error::of(new LogicException('Frodo Bolsón'));
        $this->errorWithMessage = Error::of('Bilbo Bolsón');
        $this->ok = Ok::empty();
    }

    /**
     * @psalm-suppress InaccessibleMethod
     */
    public function testPrivateConstructor(): void
    {
        self::assertException(
            \Error::class,
            fn() => new Error(null) // @phpstan-ignore-line
        );
    }

    public function testResultType(): void
    {
        self::assertTrue($this->emptyError->isError());
        self::assertTrue($this->errorWithException->isError());
        self::assertTrue($this->errorWithMessage->isError());

        self::assertFalse($this->emptyError->isOk());
        self::assertFalse($this->errorWithException->isOk());
        self::assertFalse($this->errorWithMessage->isOk());
    }

    public function testResultValue(): void
    {
        self::assertFalse($this->emptyError->hasValue());
        self::assertTrue($this->errorWithException->hasValue());
        self::assertTrue($this->errorWithMessage->hasValue());

        self::assertNull($this->emptyError->value());
        self::assertInstanceOf(LogicException::class, $this->errorWithException->value());
        self::assertSame('Bilbo Bolsón', $this->errorWithMessage->value());
    }

    public function testBooleanOperations(): void
    {
        self::assertTrue($this->emptyError->or(true));
        self::assertTrue($this->errorWithException->or(true));
        self::assertTrue($this->errorWithMessage->or(true));

        self::assertFalse($this->emptyError->orFalse());
        self::assertFalse($this->errorWithException->orFalse());
        self::assertFalse($this->errorWithMessage->orFalse());

        self::assertNull($this->emptyError->orNull());
        self::assertNull($this->errorWithException->orNull());
        self::assertNull($this->errorWithMessage->orNull());

        self::assertException(
            RuntimeException::class,
            fn() => $this->emptyError->orFail(),
        );
        self::assertException(
            LogicException::class,
            fn() => $this->errorWithException->orFail(),
        );
        self::assertException(
            RuntimeException::class,
            fn() => $this->errorWithMessage->orFail(),
        );

        self::assertExceptionMessage(
            '',
            fn() => $this->emptyError->orFail(),
        );
        self::assertExceptionMessage(
            'Frodo Bolsón',
            fn() => $this->errorWithException->orFail(),
        );
        self::assertExceptionMessage(
            '',
            fn() => $this->errorWithMessage->orFail(),
        );

        self::assertException(
            UnexpectedValueException::class,
            fn() => $this->emptyError->orThrow(new UnexpectedValueException()),
        );
        self::assertExceptionMessage(
            'The result was an error',
            fn() => $this->errorWithException->orThrow(new UnexpectedValueException('The result was an error')),
        );
        self::assertExceptionMessage(
            'The result was an error',
            fn() => $this->errorWithMessage->orThrow(new UnexpectedValueException('The result was an error')),
        );

        self::assertException(
            UnexpectedValueException::class,
            fn() => $this->emptyError->orThrow(fn() => new UnexpectedValueException()),
        );
        self::assertExceptionMessage(
            'The result was an error',
            fn() => $this->errorWithException->orThrow(fn(Throwable $e) => new UnexpectedValueException('The result was an error', previous: $e)),
        );
        self::assertExceptionMessage(
            'The result was an error',
            fn() => $this->errorWithMessage->orThrow(fn() => new UnexpectedValueException('The result was an error')),
        );

        self::assertSame(
            $this->ok,
            $this->emptyError->orElse($this->ok)
        );
        self::assertSame(
            $this->ok,
            $this->errorWithException->orElse($this->ok)
        );
        self::assertSame(
            $this->ok,
            $this->errorWithMessage->orElse($this->ok)
        );

        self::assertSame(
            $this->emptyError,
            $this->emptyError->andThen($this->ok)
        );
        self::assertSame(
            $this->errorWithException,
            $this->errorWithException->andThen($this->ok)
        );
        self::assertSame(
            $this->errorWithMessage,
            $this->errorWithMessage->andThen($this->ok)
        );
    }

    public function testBooleanOperationsWithClosures(): void
    {
        self::assertTrue(
            $this->emptyError->or(fn() => true)
        );
        self::assertTrue(
            $this->errorWithException->or(fn() => true)
        );
        self::assertTrue(
            $this->errorWithMessage->or(fn() => true)
        );

        $randomResult = function (): Ok|Error {
            return ($this->random()->boolean())
                ? Ok::of(true)
                : Error::of('false');
        };

        self::assertNotSame(
            $this->emptyError,
            $this->emptyError->orElse($randomResult)
        );
        self::assertNotSame(
            $this->errorWithException,
            $this->errorWithException->orElse($randomResult)
        );
        self::assertNotSame(
            $this->errorWithMessage,
            $this->errorWithMessage->orElse($randomResult)
        );

        self::assertSame(
            $this->emptyError,
            $this->emptyError->andThen($randomResult)
        );
        self::assertSame(
            $this->errorWithException,
            $this->errorWithException->andThen($randomResult)
        );
        self::assertSame(
            $this->errorWithMessage,
            $this->errorWithMessage->andThen($randomResult)
        );
    }

    public function testActions(): void
    {
        self::assertSame(
            $this->emptyError,
            $this->emptyError->onSuccess(fn() => throw new Exception())
        );
        self::assertException(
            Exception::class,
            function (): void {
                $this->emptyError->onFailure(fn() => throw new Exception());
            }
        );

        self::assertSame(
            $this->errorWithException,
            $this->errorWithException->onSuccess(fn() => throw new Exception())
        );
        self::assertException(
            Exception::class,
            function (): void {
                $this->errorWithException->onFailure(fn() => throw new Exception());
            }
        );

        self::assertSame(
            $this->errorWithMessage,
            $this->errorWithMessage->onSuccess(fn() => throw new Exception())
        );
        self::assertException(
            Exception::class,
            function (): void {
                $this->errorWithMessage->onFailure(fn() => throw new Exception());
            }
        );
    }
}
