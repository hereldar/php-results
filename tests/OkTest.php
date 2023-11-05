<?php

declare(strict_types=1);

namespace Hereldar\Results\Tests;

use Exception;
use Hereldar\Results\Error;
use Hereldar\Results\Ok;
use Throwable;
use UnexpectedValueException;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
final class OkTest extends TestCase
{
    private Ok $emptyOk;
    private Ok $okWithValue;
    private Error $error;

    protected function setUp(): void
    {
        parent::setUp();

        $this->emptyOk = Ok::empty();
        $this->okWithValue = Ok::withValue(42);
        $this->error = Error::empty();
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

        self::assertNull($this->emptyOk->orFalse());
        self::assertSame(42, $this->okWithValue->orFalse());

        self::assertNull($this->emptyOk->orNull());
        self::assertSame(42, $this->okWithValue->orNull());

        self::assertNull($this->emptyOk->orFail());
        self::assertSame(42, $this->okWithValue->orFail());

        self::assertNull($this->emptyOk->orThrow(new UnexpectedValueException()));
        self::assertSame(42, $this->okWithValue->orThrow(new UnexpectedValueException()));

        self::assertNull($this->emptyOk->orThrow(fn(Throwable $e) => new UnexpectedValueException(previous: $e)));
        self::assertSame(42, $this->okWithValue->orThrow(fn(Throwable $e) => new UnexpectedValueException(previous: $e)));

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
            $this->emptyOk->or(fn() => true)
        );
        self::assertSame(
            42,
            $this->okWithValue->or(fn() => true)
        );

        $randomResult = function (): Ok|Error {
            return ($this->random()->boolean())
                ? Ok::withValue(true)
                : Error::withMessage('false');
        };

        self::assertSame(
            $this->emptyOk,
            $this->emptyOk->orElse($randomResult)
        );
        self::assertSame(
            $this->okWithValue,
            $this->okWithValue->orElse($randomResult)
        );

        self::assertNotSame(
            $this->emptyOk,
            $this->emptyOk->andThen($randomResult)
        );
        self::assertNotSame(
            $this->okWithValue,
            $this->okWithValue->andThen($randomResult)
        );
    }

    public function testActions(): void
    {
        self::assertException(
            Exception::class,
            function (): void {
                $this->emptyOk->onSuccess(fn() => throw new Exception());
            }
        );
        self::assertSame(
            $this->emptyOk,
            $this->emptyOk->onFailure(fn() => throw new Exception())
        );

        self::assertException(
            Exception::class,
            function (): void {
                $this->okWithValue->onSuccess(fn() => throw new Exception());
            }
        );
        self::assertSame(
            $this->okWithValue,
            $this->okWithValue->onFailure(fn() => throw new Exception())
        );
    }
}
