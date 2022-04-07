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
use UnexpectedValueException;

/**
 * @covers \Hereldar\Results\AbstractResult
 * @covers \Hereldar\Results\AggregateResult
 * @covers \Hereldar\Results\Exceptions\AggregateException
 */
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
        $this->assertFalse($this->emptyResult->isError());
        $this->assertFalse($this->resultWithOks->isError());
        $this->assertTrue($this->resultWithErrors->isError());
        $this->assertTrue($this->resultWithErrorsAndOks->isError());

        $this->assertTrue($this->emptyResult->isOk());
        $this->assertTrue($this->resultWithOks->isOk());
        $this->assertFalse($this->resultWithErrors->isOk());
        $this->assertFalse($this->resultWithErrorsAndOks->isOk());
    }

    public function testResultException(): void
    {
        $this->assertFalse($this->emptyResult->hasException());
        $this->assertFalse($this->resultWithOks->hasException());
        $this->assertTrue($this->resultWithErrors->hasException());
        $this->assertTrue($this->resultWithErrorsAndOks->hasException());

        $this->assertNull($this->emptyResult->exception());
        $this->assertNull($this->resultWithOks->exception());
        $this->assertInstanceOf(AggregateException::class, $this->resultWithErrors->exception());
        $this->assertInstanceOf(AggregateException::class, $this->resultWithErrorsAndOks->exception());
    }

    public function testResultMessage(): void
    {
        $this->assertFalse($this->emptyResult->hasMessage());
        $this->assertFalse($this->resultWithOks->hasMessage());
        $this->assertFalse($this->resultWithErrors->hasMessage());
        $this->assertFalse($this->resultWithErrorsAndOks->hasMessage());

        $this->assertSame('', $this->emptyResult->message());
        $this->assertSame('', $this->resultWithOks->message());
        $this->assertSame('', $this->resultWithErrors->message());
        $this->assertSame('', $this->resultWithErrorsAndOks->message());
    }

    public function testResultValue(): void
    {
        $this->assertFalse($this->emptyResult->hasValue());
        $this->assertFalse($this->resultWithOks->hasValue());
        $this->assertFalse($this->resultWithErrors->hasValue());
        $this->assertFalse($this->resultWithErrorsAndOks->hasValue());

        $this->assertNull($this->emptyResult->value());
        $this->assertNull($this->resultWithOks->value());
        $this->assertNull($this->resultWithErrors->value());
        $this->assertNull($this->resultWithErrorsAndOks->value());
    }

    public function testBooleanOperations(): void
    {
        $this->assertNull($this->emptyResult->or(true));
        $this->assertNull($this->resultWithOks->or(true));
        $this->assertTrue($this->resultWithErrors->or(true));
        $this->assertTrue($this->resultWithErrorsAndOks->or(true));

        $this->assertNull($this->emptyResult->orNull());
        $this->assertNull($this->resultWithOks->orNull());
        $this->assertNull($this->resultWithErrors->orNull());
        $this->assertNull($this->resultWithErrorsAndOks->orNull());

        $this->assertNull($this->emptyResult->orFail());
        $this->assertNull($this->resultWithOks->orFail());
        $this->assertException(
            AggregateException::class,
            fn () => $this->resultWithErrors->orFail(),
        );
        $this->assertException(
            AggregateException::class,
            fn () => $this->resultWithErrorsAndOks->orFail(),
        );

        $this->assertNull($this->emptyResult->orThrow(new UnexpectedValueException()));
        $this->assertNull($this->resultWithOks->orThrow(new UnexpectedValueException()));
        $this->assertException(
            UnexpectedValueException::class,
            fn () => $this->resultWithErrors->orThrow(new UnexpectedValueException()),
        );
        $this->assertExceptionMessage(
            'The result was an error',
            fn () => $this->resultWithErrorsAndOks->orThrow(new UnexpectedValueException('The result was an error')),
        );

        $this->assertSame(
            $this->emptyResult,
            $this->emptyResult->orElse($this->error)
        );
        $this->assertSame(
            $this->resultWithOks,
            $this->resultWithOks->orElse($this->error)
        );
        $this->assertSame(
            $this->ok,
            $this->resultWithErrors->orElse($this->ok)
        );
        $this->assertSame(
            $this->ok,
            $this->resultWithErrorsAndOks->orElse($this->ok)
        );

        $this->assertSame(
            $this->error,
            $this->emptyResult->andThen($this->error)
        );
        $this->assertSame(
            $this->error,
            $this->resultWithOks->andThen($this->error)
        );
        $this->assertSame(
            $this->resultWithErrors,
            $this->resultWithErrors->andThen($this->ok)
        );
        $this->assertSame(
            $this->resultWithErrorsAndOks,
            $this->resultWithErrorsAndOks->andThen($this->ok)
        );
    }

    public function testBooleanOperationsWithClosures(): void
    {
        $this->assertNull(
            $this->emptyResult->or(function () {
                return true;
            })
        );
        $this->assertNull(
            $this->resultWithOks->or(function () {
                return true;
            })
        );
        $this->assertTrue(
            $this->resultWithErrors->or(function () {
                return true;
            })
        );
        $this->assertTrue(
            $this->resultWithErrorsAndOks->or(function () {
                return true;
            })
        );

        $this->assertSame(
            $this->emptyResult,
            $this->emptyResult->orElse(function () {
                return $this->error;
            })
        );
        $this->assertSame(
            $this->resultWithOks,
            $this->resultWithOks->orElse(function () {
                return $this->error;
            })
        );
        $this->assertSame(
            $this->ok,
            $this->resultWithErrors->orElse(function () {
                return $this->ok;
            })
        );
        $this->assertSame(
            $this->ok,
            $this->resultWithErrorsAndOks->orElse(function () {
                return $this->ok;
            })
        );

        $this->assertSame(
            $this->error,
            $this->emptyResult->andThen(function () {
                return $this->error;
            })
        );
        $this->assertSame(
            $this->error,
            $this->resultWithOks->andThen(function () {
                return $this->error;
            })
        );
        $this->assertSame(
            $this->resultWithErrors,
            $this->resultWithErrors->andThen(function () {
                return $this->ok;
            })
        );
        $this->assertSame(
            $this->resultWithErrorsAndOks,
            $this->resultWithErrorsAndOks->andThen(function () {
                return $this->ok;
            })
        );
    }

    public function testBooleanOperationsWithArrowFunctions(): void
    {
        $this->assertNull(
            $this->emptyResult->or(fn () => true)
        );
        $this->assertNull(
            $this->resultWithOks->or(fn () => true)
        );
        $this->assertTrue(
            $this->resultWithErrors->or(fn () => true)
        );
        $this->assertTrue(
            $this->resultWithErrorsAndOks->or(fn () => true)
        );

        $this->assertSame(
            $this->emptyResult,
            $this->emptyResult->orElse(fn () => $this->error)
        );
        $this->assertSame(
            $this->resultWithOks,
            $this->resultWithOks->orElse(fn () => $this->error)
        );
        $this->assertSame(
            $this->ok,
            $this->resultWithErrors->orElse(fn () => $this->ok)
        );
        $this->assertSame(
            $this->ok,
            $this->resultWithErrorsAndOks->orElse(fn () => $this->ok)
        );

        $this->assertSame(
            $this->error,
            $this->emptyResult->andThen(fn () => $this->error)
        );
        $this->assertSame(
            $this->error,
            $this->resultWithOks->andThen(fn () => $this->error)
        );
        $this->assertSame(
            $this->resultWithErrors,
            $this->resultWithErrors->andThen(fn () => $this->ok)
        );
        $this->assertSame(
            $this->resultWithErrorsAndOks,
            $this->resultWithErrorsAndOks->andThen(fn () => $this->ok)
        );
    }

    public function testIndividualResults(): void
    {
        $this->assertTrue($this->emptyResult->isEmpty());
        $this->assertFalse($this->resultWithOks->isEmpty());
        $this->assertFalse($this->resultWithErrors->isEmpty());
        $this->assertFalse($this->resultWithErrorsAndOks->isEmpty());

        $this->assertCount(0, $this->emptyResult->individualResults());
        $this->assertCount(2, $this->resultWithOks->individualResults());
        $this->assertCount(2, $this->resultWithErrors->individualResults());
        $this->assertCount(2, $this->resultWithErrorsAndOks->individualResults());

        $this->assertCount(0, $this->emptyResult->individualErrors());
        $this->assertCount(0, $this->resultWithOks->individualErrors());
        $this->assertCount(2, $this->resultWithErrors->individualErrors());
        $this->assertCount(1, $this->resultWithErrorsAndOks->individualErrors());
    }

    public function testAggregateException(): void
    {
        try {
            $this->resultWithErrors->orFail();
        } catch (IAggregateException $exceptionWithErrors) {
        }

        try {
            $this->resultWithErrorsAndOks->orFail();
        } catch (IAggregateException $exceptionWithErrorsAndOks) {
        }

        $this->assertCount(2, $exceptionWithErrors->getResults());
        $this->assertCount(2, $exceptionWithErrorsAndOks->getResults());

        $this->assertCount(2, $exceptionWithErrors->getErrors());
        $this->assertCount(1, $exceptionWithErrorsAndOks->getErrors());
    }

    public function testUnusedException(): void
    {
        $this->assertException(
            UnusedResult::class,
            function () {
                $result = new AggregateResult();
                unset($result);

                throw new Exception();
            }
        );

        $this->assertException(
            UnusedResult::class,
            function () {
                AggregateResult::empty();
            }
        );
    }
}
