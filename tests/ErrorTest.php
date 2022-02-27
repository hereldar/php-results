<?php

declare(strict_types=1);

namespace Hereldar\Results\Tests;

use Hereldar\Results\Error;
use Hereldar\Results\Ok;

final class ErrorTest extends TestCase
{
    private Error $emptyError;
    private Error $errorWithMessage;
    private Ok $ok;

    public function setUp(): void
    {
        parent::setUp();

        $this->emptyError = Error::empty();
        $this->errorWithMessage = new Error('Bilbo Bolsón');
        $this->ok = new Ok();
    }

    public function testResultType(): void
    {
        $this->assertTrue($this->emptyError->isError());
        $this->assertTrue($this->errorWithMessage->isError());

        $this->assertFalse($this->emptyError->isOk());
        $this->assertFalse($this->errorWithMessage->isOk());
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
            Error::class,
            fn() => $this->emptyError->orFail(),
        );
        $this->assertExceptionMessage(
            'Bilbo Bolsón',
            fn() => $this->errorWithMessage->orFail(),
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
            $this->emptyError->or(fn() => true)
        );
        $this->assertTrue(
            $this->errorWithMessage->or(fn() => true)
        );

        $this->assertSame(
            $this->ok,
            $this->emptyError->orElse(fn() => $this->ok)
        );
        $this->assertSame(
            $this->ok,
            $this->errorWithMessage->orElse(fn() => $this->ok)
        );

        $this->assertSame(
            $this->emptyError,
            $this->emptyError->andThen(fn() => $this->ok)
        );
        $this->assertSame(
            $this->errorWithMessage,
            $this->errorWithMessage->andThen(fn() => $this->ok)
        );
    }
}