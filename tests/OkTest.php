<?php

declare(strict_types=1);

namespace Hereldar\Results\Tests;

use Hereldar\Results\Error;
use Hereldar\Results\Ok;

/**
 * @covers \Hereldar\Results\Ok
 */
final class OkTest extends TestCase
{
    private Ok $emptyOk;
    private Ok $okWithValue;
    private Ok $okWithMessage;
    private Error $error;

    public function setUp(): void
    {
        parent::setUp();

        $this->emptyOk = Ok::empty();
        $this->okWithValue = Ok::of(42);
        $this->okWithMessage = new Ok(message: 'Bilbo Bolsón');
        $this->error = new Error();
    }

    public function testResultType(): void
    {
        $this->assertFalse($this->emptyOk->isError());
        $this->assertFalse($this->okWithValue->isError());
        $this->assertFalse($this->okWithMessage->isError());

        $this->assertTrue($this->emptyOk->isOk());
        $this->assertTrue($this->okWithValue->isOk());
        $this->assertTrue($this->okWithMessage->isOk());
    }

    public function testResultMessage(): void
    {
        $this->assertFalse($this->emptyOk->hasMessage());
        $this->assertFalse($this->okWithValue->hasMessage());
        $this->assertTrue($this->okWithMessage->hasMessage());

        $this->assertSame('', $this->emptyOk->message());
        $this->assertSame('', $this->okWithValue->message());
        $this->assertSame('Bilbo Bolsón', $this->okWithMessage->message());
    }

    public function testResultValue(): void
    {
        $this->assertFalse($this->emptyOk->hasValue());
        $this->assertTrue($this->okWithValue->hasValue());
        $this->assertFalse($this->okWithMessage->hasValue());

        $this->assertNull($this->emptyOk->value());
        $this->assertSame(42, $this->okWithValue->value());
        $this->assertNull($this->okWithMessage->value());
    }

    public function testBooleanOperations(): void
    {
        $this->assertNull($this->emptyOk->or(true));
        $this->assertSame(42, $this->okWithValue->or(true));
        $this->assertNull($this->okWithMessage->or(true));

        $this->assertNull($this->emptyOk->orNull());
        $this->assertSame(42, $this->okWithValue->orNull());
        $this->assertNull($this->okWithMessage->orNull());

        $this->assertNull($this->emptyOk->orFail());
        $this->assertSame(42, $this->okWithValue->orFail());
        $this->assertNull($this->okWithMessage->orFail());

        $this->assertSame(
            $this->emptyOk,
            $this->emptyOk->orElse($this->error)
        );
        $this->assertSame(
            $this->okWithValue,
            $this->okWithValue->orElse($this->error)
        );
        $this->assertSame(
            $this->okWithMessage,
            $this->okWithMessage->orElse($this->error)
        );

        $this->assertSame(
            $this->error,
            $this->emptyOk->andThen($this->error)
        );
        $this->assertSame(
            $this->error,
            $this->okWithValue->andThen($this->error)
        );
        $this->assertSame(
            $this->error,
            $this->okWithMessage->andThen($this->error)
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
        $this->assertNull(
            $this->okWithMessage->or(function () {
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
            $this->okWithMessage,
            $this->okWithMessage->orElse(function () {
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
        $this->assertSame(
            $this->error,
            $this->okWithMessage->andThen(function () {
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
        $this->assertNull(
            $this->okWithMessage->or(fn () => true)
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
            $this->okWithMessage,
            $this->okWithMessage->orElse(fn () => $this->error)
        );

        $this->assertSame(
            $this->error,
            $this->emptyOk->andThen(fn () => $this->error)
        );
        $this->assertSame(
            $this->error,
            $this->okWithValue->andThen(fn () => $this->error)
        );
        $this->assertSame(
            $this->error,
            $this->okWithMessage->andThen(fn () => $this->error)
        );
    }
}
