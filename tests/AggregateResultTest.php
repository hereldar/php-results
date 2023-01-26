<?php

declare(strict_types=1);

namespace Hereldar\Results\Tests;

use Exception;
use Hereldar\Results\AggregateResult;
use Hereldar\Results\Error;
use Hereldar\Results\Exceptions\AggregateException;
use Hereldar\Results\Exceptions\UnusedResult;
use Hereldar\Results\Interfaces\IAggregateException;
use Hereldar\Results\Ok;
use Throwable;
use UnexpectedValueException;

final class AggregateResultTest extends TestCase
{
    private Error $error;
    private Ok $ok;

    private AggregateResult $emptyResult;
    private AggregateResult $resultWithOks;
    private AggregateResult $resultWithErrors;
    private AggregateResult $resultWithErrorsAndOks;

    public function setUp(): void
    {
        parent::setUp();

        $this->error = Error::empty();
        $this->ok = Ok::empty();

        $this->emptyResult = AggregateResult::empty();
        $this->resultWithOks = AggregateResult::of(Ok::empty(), Ok::empty());
        $this->resultWithErrors = AggregateResult::of(Error::empty(), Error::empty());
        $this->resultWithErrorsAndOks = AggregateResult::of(Error::empty(), Ok::empty());
    }

    public function tearDown(): void
    {
        // We make sure that all results have been used before
        // destroying them.

        $this->error->value();
        $this->ok->value();

        $this->emptyResult->value();
        $this->resultWithOks->value();
        $this->resultWithErrors->value();
        $this->resultWithErrorsAndOks->value();
    }

    public function testResultType(): void
    {
        self::assertFalse($this->emptyResult->isError());
        self::assertFalse($this->resultWithOks->isError());
        self::assertTrue($this->resultWithErrors->isError());
        self::assertTrue($this->resultWithErrorsAndOks->isError());

        self::assertTrue($this->emptyResult->isOk());
        self::assertTrue($this->resultWithOks->isOk());
        self::assertFalse($this->resultWithErrors->isOk());
        self::assertFalse($this->resultWithErrorsAndOks->isOk());
    }

    public function testResultException(): void
    {
        self::assertFalse($this->emptyResult->hasException());
        self::assertFalse($this->resultWithOks->hasException());
        self::assertTrue($this->resultWithErrors->hasException());
        self::assertTrue($this->resultWithErrorsAndOks->hasException());

        self::assertNull($this->emptyResult->exception());
        self::assertNull($this->resultWithOks->exception());
        self::assertInstanceOf(AggregateException::class, $this->resultWithErrors->exception());
        self::assertInstanceOf(AggregateException::class, $this->resultWithErrorsAndOks->exception());
    }

    public function testResultMessage(): void
    {
        self::assertFalse($this->emptyResult->hasMessage());
        self::assertFalse($this->resultWithOks->hasMessage());
        self::assertFalse($this->resultWithErrors->hasMessage());
        self::assertFalse($this->resultWithErrorsAndOks->hasMessage());

        self::assertSame('', $this->emptyResult->message());
        self::assertSame('', $this->resultWithOks->message());
        self::assertSame('', $this->resultWithErrors->message());
        self::assertSame('', $this->resultWithErrorsAndOks->message());
    }

    public function testResultValue(): void
    {
        self::assertFalse($this->emptyResult->hasValue());
        self::assertFalse($this->resultWithOks->hasValue());
        self::assertFalse($this->resultWithErrors->hasValue());
        self::assertFalse($this->resultWithErrorsAndOks->hasValue());

        self::assertNull($this->emptyResult->value());
        self::assertNull($this->resultWithOks->value());
        self::assertNull($this->resultWithErrors->value());
        self::assertNull($this->resultWithErrorsAndOks->value());
    }

    public function testBooleanOperations(): void
    {
        self::assertNull($this->emptyResult->or(true));
        self::assertNull($this->resultWithOks->or(true));
        self::assertTrue($this->resultWithErrors->or(true));
        self::assertTrue($this->resultWithErrorsAndOks->or(true));

        self::assertNull($this->emptyResult->orFalse());
        self::assertNull($this->resultWithOks->orFalse());
        self::assertFalse($this->resultWithErrors->orFalse());
        self::assertFalse($this->resultWithErrorsAndOks->orFalse());

        self::assertNull($this->emptyResult->orNull());
        self::assertNull($this->resultWithOks->orNull());
        self::assertNull($this->resultWithErrors->orNull());
        self::assertNull($this->resultWithErrorsAndOks->orNull());

        self::assertNull($this->emptyResult->orFail());
        self::assertNull($this->resultWithOks->orFail());
        self::assertException(
            AggregateException::class,
            fn () => $this->resultWithErrors->orFail(),
        );
        self::assertException(
            AggregateException::class,
            fn () => $this->resultWithErrorsAndOks->orFail(),
        );

        self::assertNull($this->emptyResult->orThrow(new UnexpectedValueException()));
        self::assertNull($this->resultWithOks->orThrow(new UnexpectedValueException()));
        self::assertException(
            UnexpectedValueException::class,
            fn () => $this->resultWithErrors->orThrow(new UnexpectedValueException()),
        );
        self::assertExceptionMessage(
            'The result was an error',
            fn () => $this->resultWithErrorsAndOks->orThrow(new UnexpectedValueException('The result was an error')),
        );

        self::assertNull($this->emptyResult->orThrow(fn ($e) => new UnexpectedValueException(previous: $e)));
        self::assertNull($this->resultWithOks->orThrow(fn ($e) => new UnexpectedValueException(previous: $e)));
        self::assertException(
            UnexpectedValueException::class,
            fn () => $this->resultWithErrors->orThrow(fn ($e) => new UnexpectedValueException(previous: $e)),
        );
        self::assertExceptionMessage(
            'The result was an error',
            fn () => $this->resultWithErrorsAndOks->orThrow(fn ($e) => new UnexpectedValueException('The result was an error', previous: $e)),
        );

        self::assertSame(
            $this->emptyResult,
            $this->emptyResult->orElse($this->error)
        );
        self::assertSame(
            $this->resultWithOks,
            $this->resultWithOks->orElse($this->error)
        );
        self::assertSame(
            $this->ok,
            $this->resultWithErrors->orElse($this->ok)
        );
        self::assertSame(
            $this->ok,
            $this->resultWithErrorsAndOks->orElse($this->ok)
        );

        self::assertSame(
            $this->error,
            $this->emptyResult->andThen($this->error)
        );
        self::assertSame(
            $this->error,
            $this->resultWithOks->andThen($this->error)
        );
        self::assertSame(
            $this->resultWithErrors,
            $this->resultWithErrors->andThen($this->ok)
        );
        self::assertSame(
            $this->resultWithErrorsAndOks,
            $this->resultWithErrorsAndOks->andThen($this->ok)
        );
    }

    public function testBooleanOperationsWithClosures(): void
    {
        self::assertNull(
            $this->emptyResult->or(function () {
                return true;
            })
        );
        self::assertNull(
            $this->resultWithOks->or(function () {
                return true;
            })
        );
        self::assertTrue(
            $this->resultWithErrors->or(function () {
                return true;
            })
        );
        self::assertTrue(
            $this->resultWithErrorsAndOks->or(function () {
                return true;
            })
        );

        self::assertSame(
            $this->emptyResult,
            $this->emptyResult->orElse(function () {
                return $this->error;
            })
        );
        self::assertSame(
            $this->resultWithOks,
            $this->resultWithOks->orElse(function () {
                return $this->error;
            })
        );
        self::assertSame(
            $this->ok,
            $this->resultWithErrors->orElse(function () {
                return $this->ok;
            })
        );
        self::assertSame(
            $this->ok,
            $this->resultWithErrorsAndOks->orElse(function () {
                return $this->ok;
            })
        );

        self::assertSame(
            $this->error,
            $this->emptyResult->andThen(function () {
                return $this->error;
            })
        );
        self::assertSame(
            $this->error,
            $this->resultWithOks->andThen(function () {
                return $this->error;
            })
        );
        self::assertSame(
            $this->resultWithErrors,
            $this->resultWithErrors->andThen(function () {
                return $this->ok;
            })
        );
        self::assertSame(
            $this->resultWithErrorsAndOks,
            $this->resultWithErrorsAndOks->andThen(function () {
                return $this->ok;
            })
        );
    }

    public function testBooleanOperationsWithArrowFunctions(): void
    {
        self::assertNull(
            $this->emptyResult->or(fn () => true)
        );
        self::assertNull(
            $this->resultWithOks->or(fn () => true)
        );
        self::assertTrue(
            $this->resultWithErrors->or(fn () => true)
        );
        self::assertTrue(
            $this->resultWithErrorsAndOks->or(fn () => true)
        );

        self::assertSame(
            $this->emptyResult,
            $this->emptyResult->orElse(fn () => $this->error)
        );
        self::assertSame(
            $this->resultWithOks,
            $this->resultWithOks->orElse(fn () => $this->error)
        );
        self::assertSame(
            $this->ok,
            $this->resultWithErrors->orElse(fn () => $this->ok)
        );
        self::assertSame(
            $this->ok,
            $this->resultWithErrorsAndOks->orElse(fn () => $this->ok)
        );

        self::assertSame(
            $this->error,
            $this->emptyResult->andThen(fn () => $this->error)
        );
        self::assertSame(
            $this->error,
            $this->resultWithOks->andThen(fn () => $this->error)
        );
        self::assertSame(
            $this->resultWithErrors,
            $this->resultWithErrors->andThen(fn () => $this->ok)
        );
        self::assertSame(
            $this->resultWithErrorsAndOks,
            $this->resultWithErrorsAndOks->andThen(fn () => $this->ok)
        );
    }

    public function testActions(): void
    {
        self::assertException(
            Exception::class,
            function () {
                $this->emptyResult->onSuccess(fn () => throw new Exception());
            }
        );
        self::assertSame(
            $this->emptyResult,
            $this->emptyResult->onFailure(fn () => throw new Exception())
        );

        self::assertException(
            Exception::class,
            function () {
                $this->resultWithOks->onSuccess(fn () => throw new Exception());
            }
        );
        self::assertSame(
            $this->resultWithOks,
            $this->resultWithOks->onFailure(fn () => throw new Exception())
        );

        self::assertSame(
            $this->resultWithErrors,
            $this->resultWithErrors->onSuccess(fn () => throw new Exception())
        );
        self::assertException(
            Exception::class,
            function () {
                $this->resultWithErrors->onFailure(fn () => throw new Exception());
            }
        );

        self::assertSame(
            $this->resultWithErrorsAndOks,
            $this->resultWithErrorsAndOks->onSuccess(fn () => throw new Exception())
        );
        self::assertException(
            Exception::class,
            function () {
                $this->resultWithErrorsAndOks->onFailure(fn () => throw new Exception());
            }
        );
    }

    public function testIndividualResults(): void
    {
        self::assertTrue($this->emptyResult->isEmpty());
        self::assertFalse($this->resultWithOks->isEmpty());
        self::assertFalse($this->resultWithErrors->isEmpty());
        self::assertFalse($this->resultWithErrorsAndOks->isEmpty());

        self::assertCount(0, $this->emptyResult->individualResults());
        self::assertCount(2, $this->resultWithOks->individualResults());
        self::assertCount(2, $this->resultWithErrors->individualResults());
        self::assertCount(2, $this->resultWithErrorsAndOks->individualResults());

        self::assertCount(0, $this->emptyResult->individualErrors());
        self::assertCount(0, $this->resultWithOks->individualErrors());
        self::assertCount(2, $this->resultWithErrors->individualErrors());
        self::assertCount(1, $this->resultWithErrorsAndOks->individualErrors());
    }

    public function testAggregateException(): void
    {
        try {
            $this->resultWithErrors->orFail();
        } catch (IAggregateException $exceptionWithErrors) {
            self::assertCount(2, $exceptionWithErrors->getResults());
            self::assertCount(2, $exceptionWithErrors->getErrors());
        }

        try {
            $this->resultWithErrorsAndOks->orFail();
        } catch (IAggregateException $exceptionWithErrorsAndOks) {
            self::assertCount(2, $exceptionWithErrorsAndOks->getResults());
            self::assertCount(1, $exceptionWithErrorsAndOks->getErrors());
        }
    }

    public function testUnusedException(): void
    {
        self::assertException(
            UnusedResult::class,
            static function () {
                $result = new AggregateResult();
                unset($result);

                throw new Exception();
            }
        );

        self::assertException(
            UnusedResult::class,
            static function () {
                AggregateResult::empty();
            }
        );
    }
}
