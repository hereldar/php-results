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

/**
 * @covers \Hereldar\Results\AbstractThrowableError
 */
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
        $this->assertTrue($this->emptyError->isError());
        $this->assertTrue($this->errorWithMessage->isError());

        $this->assertFalse($this->emptyError->isOk());
        $this->assertFalse($this->errorWithMessage->isOk());
    }

    public function testResultException(): void
    {
        $this->assertTrue($this->emptyError->hasException());
        $this->assertTrue($this->errorWithMessage->hasException());

        $this->assertInstanceOf(Throwable::class, $this->emptyError->exception());
        $this->assertInstanceOf(Throwable::class, $this->errorWithMessage->exception());

        $this->assertSame($this->emptyError, $this->emptyError->exception());
        $this->assertSame($this->errorWithMessage, $this->errorWithMessage->exception());
    }

    public function testResultMessage(): void
    {
        $this->assertFalse($this->emptyError->hasMessage());
        $this->assertTrue($this->errorWithMessage->hasMessage());

        $this->assertSame('', $this->emptyError->message());
        $this->assertSame('Bilbo Bolsón', $this->errorWithMessage->message());
    }

    public function testResultValue(): void
    {
        $this->assertFalse($this->emptyError->hasValue());
        $this->assertFalse($this->errorWithMessage->hasValue());

        $this->assertNull($this->emptyError->value());
        $this->assertNull($this->errorWithMessage->value());
    }

    public function testBooleanOperations(): void
    {
        $this->assertTrue($this->emptyError->or(true));
        $this->assertTrue($this->errorWithMessage->or(true));

        $this->assertNull($this->emptyError->orNull());
        $this->assertNull($this->errorWithMessage->orNull());

        $this->assertException(
            CustomError::class,
            fn () => $this->emptyError->orFail(),
        );
        $this->assertExceptionMessage(
            'Bilbo Bolsón',
            fn () => $this->errorWithMessage->orFail(),
        );

        $this->assertException(
            UnexpectedValueException::class,
            fn () => $this->emptyError->orThrow(new UnexpectedValueException()),
        );
        $this->assertExceptionMessage(
            'The result was an error',
            fn () => $this->errorWithMessage->orThrow(new UnexpectedValueException('The result was an error')),
        );

        $this->assertSame(
            $this->ok,
            $this->emptyError->orElse($this->ok)
        );
        $this->assertSame(
            $this->ok,
            $this->errorWithMessage->orElse($this->ok)
        );

        $this->assertSame(
            $this->emptyError,
            $this->emptyError->andThen($this->ok)
        );
        $this->assertSame(
            $this->errorWithMessage,
            $this->errorWithMessage->andThen($this->ok)
        );
    }

    public function testBooleanOperationsWithClosures(): void
    {
        $this->assertTrue(
            $this->emptyError->or(function () {
                return true;
            })
        );
        $this->assertTrue(
            $this->errorWithMessage->or(function () {
                return true;
            })
        );

        $this->assertSame(
            $this->ok,
            $this->emptyError->orElse(function () {
                return $this->ok;
            })
        );
        $this->assertSame(
            $this->ok,
            $this->errorWithMessage->orElse(function () {
                return $this->ok;
            })
        );

        $this->assertSame(
            $this->emptyError,
            $this->emptyError->andThen(function () {
                return $this->ok;
            })
        );
        $this->assertSame(
            $this->errorWithMessage,
            $this->errorWithMessage->andThen(function () {
                return $this->ok;
            })
        );
    }

    public function testBooleanOperationsWithArrowFunctions(): void
    {
        $this->assertTrue(
            $this->emptyError->or(fn () => true)
        );
        $this->assertTrue(
            $this->errorWithMessage->or(fn () => true)
        );

        $this->assertSame(
            $this->ok,
            $this->emptyError->orElse(fn () => $this->ok)
        );
        $this->assertSame(
            $this->ok,
            $this->errorWithMessage->orElse(fn () => $this->ok)
        );

        $this->assertSame(
            $this->emptyError,
            $this->emptyError->andThen(fn () => $this->ok)
        );
        $this->assertSame(
            $this->errorWithMessage,
            $this->errorWithMessage->andThen(fn () => $this->ok)
        );
    }

    public function testUnusedException(): void
    {
        $this->assertException(
            UnusedResult::class,
            function () {
                $result = new CustomError();
                unset($result);

                throw new Exception();
            }
        );

        $this->assertException(
            UnusedResult::class,
            function () {
                new CustomError();
            }
        );
    }
}
