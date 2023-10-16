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
 * @psalm-suppress PropertyNotSetInConstructor
 */
final class ErrorTest extends TestCase
{
    private Error $emptyError;
    private Error $errorFromException;
    private Error $errorWithMessage;
    private Ok $ok;

    protected function setUp(): void
    {
        parent::setUp();

        $this->emptyError = Error::empty();
        $this->errorFromException = Error::withException(new LogicException('Frodo Bolsón'));
        $this->errorWithMessage = Error::withMessage('Bilbo Bolsón');
        $this->ok = Ok::empty();
    }

    public function testResultType(): void
    {
        self::assertTrue($this->emptyError->isError());
        self::assertTrue($this->errorFromException->isError());
        self::assertTrue($this->errorWithMessage->isError());

        self::assertFalse($this->emptyError->isOk());
        self::assertFalse($this->errorFromException->isOk());
        self::assertFalse($this->errorWithMessage->isOk());
    }

    public function testResultException(): void
    {
        self::assertTrue($this->emptyError->hasException());
        self::assertTrue($this->errorFromException->hasException());
        self::assertTrue($this->errorWithMessage->hasException());

        self::assertInstanceOf(RuntimeException::class, $this->emptyError->exception());
        self::assertInstanceOf(LogicException::class, $this->errorFromException->exception());
        self::assertInstanceOf(RuntimeException::class, $this->errorWithMessage->exception());
    }

    public function testResultMessage(): void
    {
        self::assertFalse($this->emptyError->hasMessage());
        self::assertTrue($this->errorFromException->hasMessage());
        self::assertTrue($this->errorWithMessage->hasMessage());

        self::assertSame('', $this->emptyError->message());
        self::assertSame('Frodo Bolsón', $this->errorFromException->message());
        self::assertSame('Bilbo Bolsón', $this->errorWithMessage->message());
    }

    public function testResultValue(): void
    {
        self::assertFalse($this->emptyError->hasValue());
        self::assertFalse($this->errorFromException->hasValue());
        self::assertFalse($this->errorWithMessage->hasValue());

        self::assertNull($this->emptyError->value());
        self::assertNull($this->errorFromException->value());
        self::assertNull($this->errorWithMessage->value());
    }

    public function testBooleanOperations(): void
    {
        self::assertTrue($this->emptyError->or(true));
        self::assertTrue($this->errorFromException->or(true));
        self::assertTrue($this->errorWithMessage->or(true));

        self::assertFalse($this->emptyError->orFalse());
        self::assertFalse($this->errorFromException->orFalse());
        self::assertFalse($this->errorWithMessage->orFalse());

        self::assertNull($this->emptyError->orNull());
        self::assertNull($this->errorFromException->orNull());
        self::assertNull($this->errorWithMessage->orNull());

        self::assertException(
            RuntimeException::class,
            fn() => $this->emptyError->orFail(),
        );
        self::assertException(
            LogicException::class,
            fn() => $this->errorFromException->orFail(),
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
            fn() => $this->errorFromException->orFail(),
        );
        self::assertExceptionMessage(
            'Bilbo Bolsón',
            fn() => $this->errorWithMessage->orFail(),
        );

        self::assertException(
            UnexpectedValueException::class,
            fn() => $this->emptyError->orThrow(new UnexpectedValueException()),
        );
        self::assertExceptionMessage(
            'The result was an error',
            fn() => $this->errorFromException->orThrow(new UnexpectedValueException('The result was an error')),
        );
        self::assertExceptionMessage(
            'The result was an error',
            fn() => $this->errorWithMessage->orThrow(new UnexpectedValueException('The result was an error')),
        );

        self::assertException(
            UnexpectedValueException::class,
            fn() => $this->emptyError->orThrow(fn(Throwable $e) => new UnexpectedValueException(previous: $e)),
        );
        self::assertExceptionMessage(
            'The result was an error',
            fn() => $this->errorFromException->orThrow(fn(Throwable $e) => new UnexpectedValueException('The result was an error', previous: $e)),
        );
        self::assertExceptionMessage(
            'The result was an error',
            fn() => $this->errorWithMessage->orThrow(fn(Throwable $e) => new UnexpectedValueException('The result was an error', previous: $e)),
        );

        self::assertSame(
            $this->ok,
            $this->emptyError->orElse($this->ok)
        );
        self::assertSame(
            $this->ok,
            $this->errorFromException->orElse($this->ok)
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
            $this->errorFromException,
            $this->errorFromException->andThen($this->ok)
        );
        self::assertSame(
            $this->errorWithMessage,
            $this->errorWithMessage->andThen($this->ok)
        );
    }

    public function testBooleanOperationsWithClosures(): void
    {
        self::assertTrue(
            $this->emptyError->or(function () {
                return true;
            })
        );
        self::assertTrue(
            $this->errorFromException->or(function () {
                return true;
            })
        );
        self::assertTrue(
            $this->errorWithMessage->or(function () {
                return true;
            })
        );

        $randomResult = function (): Ok|Error {
            return ($this->random()->boolean())
                ? Ok::withValue(true)
                : Error::withMessage('false');
        };

        self::assertNotSame(
            $this->emptyError,
            $this->emptyError->orElse($randomResult)
        );
        self::assertNotSame(
            $this->errorFromException,
            $this->errorFromException->orElse($randomResult)
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
            $this->errorFromException,
            $this->errorFromException->andThen($randomResult)
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
            function () {
                $this->emptyError->onFailure(fn() => throw new Exception());
            }
        );

        self::assertSame(
            $this->errorFromException,
            $this->errorFromException->onSuccess(fn() => throw new Exception())
        );
        self::assertException(
            Exception::class,
            function () {
                $this->errorFromException->onFailure(fn() => throw new Exception());
            }
        );

        self::assertSame(
            $this->errorWithMessage,
            $this->errorWithMessage->onSuccess(fn() => throw new Exception())
        );
        self::assertException(
            Exception::class,
            function () {
                $this->errorWithMessage->onFailure(fn() => throw new Exception());
            }
        );
    }
}
