<?php

declare(strict_types=1);

namespace Hereldar\Results\Tests;

use Exception;
use Hereldar\Results\Error;
use Hereldar\Results\Exceptions\UnusedResult;
use Hereldar\Results\Ok;
use UnexpectedValueException;

/**
 * @covers \Hereldar\Results\Ok
 */
final class OkTest extends TestCase
{
    private Ok $emptyOk;
    private Ok $okWithValue;
    private Error $error;

    public function setUp(): void
    {
        parent::setUp();

        $this->emptyOk = Ok::empty();
        $this->okWithValue = Ok::withValue(42);
        $this->error = Error::empty();
    }

    public function tearDown(): void
    {
        // We make sure that all results have been used before
        // destroying them.

        $this->emptyOk->value();
        $this->okWithValue->value();
        $this->error->value();
    }

    public function testResultType(): void
    {
        $this->assertFalse($this->emptyOk->isError());
        $this->assertFalse($this->okWithValue->isError());

        $this->assertTrue($this->emptyOk->isOk());
        $this->assertTrue($this->okWithValue->isOk());
    }

    public function testResultException(): void
    {
        $this->assertFalse($this->emptyOk->hasException());
        $this->assertFalse($this->okWithValue->hasException());

        $this->assertNull($this->emptyOk->exception());
        $this->assertNull($this->okWithValue->exception());
    }

    public function testResultMessage(): void
    {
        $this->assertFalse($this->emptyOk->hasMessage());
        $this->assertFalse($this->okWithValue->hasMessage());

        $this->assertSame('', $this->emptyOk->message());
        $this->assertSame('', $this->okWithValue->message());
    }

    public function testResultValue(): void
    {
        $this->assertFalse($this->emptyOk->hasValue());
        $this->assertTrue($this->okWithValue->hasValue());

        $this->assertNull($this->emptyOk->value());
        $this->assertSame(42, $this->okWithValue->value());
    }

    public function testBooleanOperations(): void
    {
        $this->assertNull($this->emptyOk->or(true));
        $this->assertSame(42, $this->okWithValue->or(true));

        $this->assertNull($this->emptyOk->orNull());
        $this->assertSame(42, $this->okWithValue->orNull());

        $this->assertNull($this->emptyOk->orFail());
        $this->assertSame(42, $this->okWithValue->orFail());

        $this->assertNull($this->emptyOk->orThrow(new UnexpectedValueException()));
        $this->assertSame(42, $this->okWithValue->orThrow(new UnexpectedValueException()));

        $this->assertSame(
            $this->emptyOk,
            $this->emptyOk->orElse($this->error)
        );
        $this->assertSame(
            $this->okWithValue,
            $this->okWithValue->orElse($this->error)
        );

        $this->assertSame(
            $this->error,
            $this->emptyOk->andThen($this->error)
        );
        $this->assertSame(
            $this->error,
            $this->okWithValue->andThen($this->error)
        );
    }

    public function testBooleanOperationsWithClosures(): void
    {
        $this->assertNull(
            $this->emptyOk->or(function () {
                return true;
            })
        );
        $this->assertSame(
            42,
            $this->okWithValue->or(function () {
                return true;
            })
        );

        $this->assertSame(
            $this->emptyOk,
            $this->emptyOk->orElse(function () {
                return $this->error;
            })
        );
        $this->assertSame(
            $this->okWithValue,
            $this->okWithValue->orElse(function () {
                return $this->error;
            })
        );

        $this->assertSame(
            $this->error,
            $this->emptyOk->andThen(function () {
                return $this->error;
            })
        );
        $this->assertSame(
            $this->error,
            $this->okWithValue->andThen(function () {
                return $this->error;
            })
        );
    }

    public function testBooleanOperationsWithArrowFunctions(): void
    {
        $this->assertNull(
            $this->emptyOk->or(fn () => true)
        );
        $this->assertSame(
            42,
            $this->okWithValue->or(fn () => true)
        );

        $this->assertSame(
            $this->emptyOk,
            $this->emptyOk->orElse(fn () => $this->error)
        );
        $this->assertSame(
            $this->okWithValue,
            $this->okWithValue->orElse(fn () => $this->error)
        );

        $this->assertSame(
            $this->error,
            $this->emptyOk->andThen(fn () => $this->error)
        );
        $this->assertSame(
            $this->error,
            $this->okWithValue->andThen(fn () => $this->error)
        );
    }

    public function testUnusedException(): void
    {
        $this->assertException(
            UnusedResult::class,
            function () {
                $result = Ok::empty();
                unset($result);

                throw new Exception();
            }
        );

        $this->assertException(
            UnusedResult::class,
            function () {
                Ok::empty();
            }
        );
    }
}
