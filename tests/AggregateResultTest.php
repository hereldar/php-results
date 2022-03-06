<?php

declare(strict_types=1);

namespace Hereldar\Results\Tests;

use Hereldar\Results\AggregateException;
use Hereldar\Results\AggregateResult;
use Hereldar\Results\Error;
use Hereldar\Results\Interfaces\IAggregateException;
use Hereldar\Results\Ok;

/**
 * @covers \Hereldar\Results\AbstractResult
 * @covers \Hereldar\Results\AggregateException
 * @covers \Hereldar\Results\AggregateResult
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

        $this->error = new Error();
        $this->ok = new Ok();

        $this->emptyResult = AggregateResult::empty();
        $this->resultWithOks = AggregateResult::of(Ok::empty(), Ok::empty());
        $this->resultWithErrors = AggregateResult::of(Error::empty(), Error::empty());
        $this->resultWithErrorsAndOks = new AggregateResult(Error::empty(), Ok::empty());
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
}
