<?php

declare(strict_types=1);

namespace Hereldar\Results\Tests;

use Exception;
use Hereldar\Results\AbstractThrowableError;
use Hereldar\Results\Exceptions\UnusedResult;
use Hereldar\Results\Ok;
use Throwable;
use UnexpectedValueException;

class CustomError extends AbstractThrowableError
{
}

final class AbstractThrowableErrorTest extends TestCase
{
    private CustomError $emptyError;
    private CustomError $errorWithMessage;
    private Ok $ok;

    public function setUp(): void
    {
        parent::setUp();

        $this->emptyError = new CustomError('');
        $this->errorWithMessage = new CustomError('Bilbo Bolsón');
        $this->ok = Ok::empty();
    }

    public function tearDown(): void
    {
        // We make sure that all results have been used before
        // destroying them.

        $this->emptyError->value();
        $this->errorWithMessage->value();
        $this->ok->value();
    }

    public function testResultType(): void
    {
        self::assertTrue($this->emptyError->isError());
        self::assertTrue($this->errorWithMessage->isError());

        self::assertFalse($this->emptyError->isOk());
        self::assertFalse($this->errorWithMessage->isOk());
    }

    public function testResultException(): void
    {
        self::assertTrue($this->emptyError->hasException());
        self::assertTrue($this->errorWithMessage->hasException());

        self::assertInstanceOf(Throwable::class, $this->emptyError->exception());
        self::assertInstanceOf(Throwable::class, $this->errorWithMessage->exception());

        self::assertSame($this->emptyError, $this->emptyError->exception());
        self::assertSame($this->errorWithMessage, $this->errorWithMessage->exception());
    }

    public function testResultMessage(): void
    {
        self::assertFalse($this->emptyError->hasMessage());
        self::assertTrue($this->errorWithMessage->hasMessage());

        self::assertSame('', $this->emptyError->message());
        self::assertSame('Bilbo Bolsón', $this->errorWithMessage->message());
    }

    public function testResultValue(): void
    {
        self::assertFalse($this->emptyError->hasValue());
        self::assertFalse($this->errorWithMessage->hasValue());

        self::assertNull($this->emptyError->value());
        self::assertNull($this->errorWithMessage->value());
    }

    public function testBooleanOperations(): void
    {
        self::assertTrue($this->emptyError->or(true));
        self::assertTrue($this->errorWithMessage->or(true));

        self::assertFalse($this->emptyError->orFalse());
        self::assertFalse($this->errorWithMessage->orFalse());

        self::assertNull($this->emptyError->orNull());
        self::assertNull($this->errorWithMessage->orNull());

        self::assertException(
            CustomError::class,
            fn () => $this->emptyError->orFail(),
        );
        self::assertExceptionMessage(
            'Bilbo Bolsón',
            fn () => $this->errorWithMessage->orFail(),
        );

        self::assertException(
            UnexpectedValueException::class,
            fn () => $this->emptyError->orThrow(new UnexpectedValueException()),
        );
        self::assertExceptionMessage(
            'The result was an error',
            fn () => $this->errorWithMessage->orThrow(new UnexpectedValueException('The result was an error')),
        );

        self::assertException(
            UnexpectedValueException::class,
            fn () => $this->emptyError->orThrow(fn ($e) => new UnexpectedValueException(previous: $e)),
        );
        self::assertExceptionMessage(
            'The result was an error',
            fn () => $this->errorWithMessage->orThrow(fn ($e) => new UnexpectedValueException('The result was an error', previous: $e)),
        );

        self::assertSame(
            $this->ok,
            $this->emptyError->orElse($this->ok)
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
            $this->errorWithMessage->or(function () {
                return true;
            })
        );

        self::assertSame(
            $this->ok,
            $this->emptyError->orElse(function () {
                return $this->ok;
            })
        );
        self::assertSame(
            $this->ok,
            $this->errorWithMessage->orElse(function () {
                return $this->ok;
            })
        );

        self::assertSame(
            $this->emptyError,
            $this->emptyError->andThen(function () {
                return $this->ok;
            })
        );
        self::assertSame(
            $this->errorWithMessage,
            $this->errorWithMessage->andThen(function () {
                return $this->ok;
            })
        );
    }

    public function testBooleanOperationsWithArrowFunctions(): void
    {
        self::assertTrue(
            $this->emptyError->or(fn () => true)
        );
        self::assertTrue(
            $this->errorWithMessage->or(fn () => true)
        );

        self::assertSame(
            $this->ok,
            $this->emptyError->orElse(fn () => $this->ok)
        );
        self::assertSame(
            $this->ok,
            $this->errorWithMessage->orElse(fn () => $this->ok)
        );

        self::assertSame(
            $this->emptyError,
            $this->emptyError->andThen(fn () => $this->ok)
        );
        self::assertSame(
            $this->errorWithMessage,
            $this->errorWithMessage->andThen(fn () => $this->ok)
        );
    }

    public function testActions(): void
    {
        self::assertSame(
            $this->emptyError,
            $this->emptyError->onSuccess(fn () => throw new Exception())
        );
        self::assertException(
            Exception::class,
            function () {
                $this->emptyError->onFailure(fn () => throw new Exception());
            }
        );

        self::assertSame(
            $this->errorWithMessage,
            $this->errorWithMessage->onSuccess(fn () => throw new Exception())
        );
        self::assertException(
            Exception::class,
            function () {
                $this->errorWithMessage->onFailure(fn () => throw new Exception());
            }
        );
    }

    public function testUnusedException(): void
    {
        self::assertException(
            UnusedResult::class,
            static function () {
                $result = new CustomError();
                unset($result);

                throw new Exception();
            }
        );

        self::assertException(
            UnusedResult::class,
            static function () {
                new CustomError();
            }
        );
    }
}
