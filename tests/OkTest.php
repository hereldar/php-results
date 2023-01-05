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
        self::assertFalse($this->emptyOk->isError());
        self::assertFalse($this->okWithValue->isError());

        self::assertTrue($this->emptyOk->isOk());
        self::assertTrue($this->okWithValue->isOk());
    }

    public function testResultException(): void
    {
        self::assertFalse($this->emptyOk->hasException());
        self::assertFalse($this->okWithValue->hasException());

        self::assertNull($this->emptyOk->exception());
        self::assertNull($this->okWithValue->exception());
    }

    public function testResultMessage(): void
    {
        self::assertFalse($this->emptyOk->hasMessage());
        self::assertFalse($this->okWithValue->hasMessage());

        self::assertSame('', $this->emptyOk->message());
        self::assertSame('', $this->okWithValue->message());
    }

    public function testResultValue(): void
    {
        self::assertFalse($this->emptyOk->hasValue());
        self::assertTrue($this->okWithValue->hasValue());

        self::assertNull($this->emptyOk->value());
        self::assertSame(42, $this->okWithValue->value());
    }

    public function testBooleanOperations(): void
    {
        self::assertNull($this->emptyOk->or(true));
        self::assertSame(42, $this->okWithValue->or(true));

        self::assertNull($this->emptyOk->orNull());
        self::assertSame(42, $this->okWithValue->orNull());

        self::assertNull($this->emptyOk->orFail());
        self::assertSame(42, $this->okWithValue->orFail());

        self::assertNull($this->emptyOk->orThrow(new UnexpectedValueException()));
        self::assertSame(42, $this->okWithValue->orThrow(new UnexpectedValueException()));

        self::assertSame(
            $this->emptyOk,
            $this->emptyOk->orElse($this->error)
        );
        self::assertSame(
            $this->okWithValue,
            $this->okWithValue->orElse($this->error)
        );

        self::assertSame(
            $this->error,
            $this->emptyOk->andThen($this->error)
        );
        self::assertSame(
            $this->error,
            $this->okWithValue->andThen($this->error)
        );
    }

    public function testBooleanOperationsWithClosures(): void
    {
        self::assertNull(
            $this->emptyOk->or(function () {
                return true;
            })
        );
        self::assertSame(
            42,
            $this->okWithValue->or(function () {
                return true;
            })
        );

        self::assertSame(
            $this->emptyOk,
            $this->emptyOk->orElse(function () {
                return $this->error;
            })
        );
        self::assertSame(
            $this->okWithValue,
            $this->okWithValue->orElse(function () {
                return $this->error;
            })
        );

        self::assertSame(
            $this->error,
            $this->emptyOk->andThen(function () {
                return $this->error;
            })
        );
        self::assertSame(
            $this->error,
            $this->okWithValue->andThen(function () {
                return $this->error;
            })
        );
    }

    public function testBooleanOperationsWithArrowFunctions(): void
    {
        self::assertNull(
            $this->emptyOk->or(fn () => true)
        );
        self::assertSame(
            42,
            $this->okWithValue->or(fn () => true)
        );

        self::assertSame(
            $this->emptyOk,
            $this->emptyOk->orElse(fn () => $this->error)
        );
        self::assertSame(
            $this->okWithValue,
            $this->okWithValue->orElse(fn () => $this->error)
        );

        self::assertSame(
            $this->error,
            $this->emptyOk->andThen(fn () => $this->error)
        );
        self::assertSame(
            $this->error,
            $this->okWithValue->andThen(fn () => $this->error)
        );
    }

    public function testActions(): void
    {
        self::assertException(
            Exception::class,
            function () {
                $this->emptyOk->onSuccess(fn () => throw new Exception());
            }
        );
        self::assertSame(
            $this->emptyOk,
            $this->emptyOk->onFailure(fn () => throw new Exception())
        );

        self::assertException(
            Exception::class,
            function () {
                $this->okWithValue->onSuccess(fn () => throw new Exception());
            }
        );
        self::assertSame(
            $this->okWithValue,
            $this->okWithValue->onFailure(fn () => throw new Exception())
        );
    }

    public function testUnusedException(): void
    {
        self::assertException(
            UnusedResult::class,
            function () {
                $result = Ok::empty();
                unset($result);

                throw new Exception();
            }
        );

        self::assertException(
            UnusedResult::class,
            function () {
                Ok::empty();
            }
        );
    }
}
