<?php

declare(strict_types=1);

namespace Hereldar\Results\Tests;

use Exception;
use Hereldar\Results\Error;
use Hereldar\Results\Exceptions\UnusedResult;
use Hereldar\Results\Ok;
use LogicException;
use RuntimeException;
use UnexpectedValueException;

/**
 * @covers \Hereldar\Results\Error
 */
final class ErrorTest extends TestCase
{
    private Error $emptyError;
    private Error $errorFromException;
    private Error $errorWithMessage;
    private Ok $ok;

    public function setUp(): void
    {
        parent::setUp();

        $this->emptyError = Error::empty();
        $this->errorFromException = Error::fromException(new LogicException('Frodo Bolsón'));
        $this->errorWithMessage = Error::withMessage('Bilbo Bolsón');
        $this->ok = Ok::empty();
    }

    public function tearDown(): void
    {
        // We make sure that all results have been used before
        // destroying them.

        $this->emptyError->value();
        $this->errorFromException->value();
        $this->errorWithMessage->value();
        $this->ok->value();
    }

    public function testResultType(): void
    {
        $this->assertTrue($this->emptyError->isError());
        $this->assertTrue($this->errorFromException->isError());
        $this->assertTrue($this->errorWithMessage->isError());

        $this->assertFalse($this->emptyError->isOk());
        $this->assertFalse($this->errorFromException->isOk());
        $this->assertFalse($this->errorWithMessage->isOk());
    }

    public function testResultException(): void
    {
        $this->assertTrue($this->emptyError->hasException());
        $this->assertTrue($this->errorFromException->hasException());
        $this->assertTrue($this->errorWithMessage->hasException());

        $this->assertInstanceOf(RuntimeException::class, $this->emptyError->exception());
        $this->assertInstanceOf(LogicException::class, $this->errorFromException->exception());
        $this->assertInstanceOf(RuntimeException::class, $this->errorWithMessage->exception());
    }

    public function testResultMessage(): void
    {
        $this->assertFalse($this->emptyError->hasMessage());
        $this->assertTrue($this->errorFromException->hasMessage());
        $this->assertTrue($this->errorWithMessage->hasMessage());

        $this->assertSame('', $this->emptyError->message());
        $this->assertSame('Frodo Bolsón', $this->errorFromException->message());
        $this->assertSame('Bilbo Bolsón', $this->errorWithMessage->message());
    }

    public function testResultValue(): void
    {
        $this->assertFalse($this->emptyError->hasValue());
        $this->assertFalse($this->errorFromException->hasValue());
        $this->assertFalse($this->errorWithMessage->hasValue());

        $this->assertNull($this->emptyError->value());
        $this->assertNull($this->errorFromException->value());
        $this->assertNull($this->errorWithMessage->value());
    }

    public function testBooleanOperations(): void
    {
        $this->assertTrue($this->emptyError->or(true));
        $this->assertTrue($this->errorFromException->or(true));
        $this->assertTrue($this->errorWithMessage->or(true));

        $this->assertNull($this->emptyError->orNull());
        $this->assertNull($this->errorFromException->orNull());
        $this->assertNull($this->errorWithMessage->orNull());

        $this->assertException(
            RuntimeException::class,
            fn () => $this->emptyError->orFail(),
        );
        $this->assertException(
            LogicException::class,
            fn () => $this->errorFromException->orFail(),
        );
        $this->assertException(
            RuntimeException::class,
            fn () => $this->errorWithMessage->orFail(),
        );

        $this->assertExceptionMessage(
            '',
            fn () => $this->emptyError->orFail(),
        );
        $this->assertExceptionMessage(
            'Frodo Bolsón',
            fn () => $this->errorFromException->orFail(),
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
            fn () => $this->errorFromException->orThrow(new UnexpectedValueException('The result was an error')),
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
            $this->errorFromException->orElse($this->ok)
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
            $this->errorFromException,
            $this->errorFromException->andThen($this->ok)
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
            $this->errorFromException->or(function () {
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
            $this->errorFromException->orElse(function () {
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
            $this->errorFromException,
            $this->errorFromException->andThen(function () {
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
            $this->errorFromException->or(fn () => true)
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
            $this->errorFromException->orElse(fn () => $this->ok)
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
            $this->errorFromException,
            $this->errorFromException->andThen(fn () => $this->ok)
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
                $result = Error::empty();
                unset($result);

                throw new Exception();
            }
        );

        $this->assertException(
            UnusedResult::class,
            function () {
                Error::empty();
            }
        );
    }
}
