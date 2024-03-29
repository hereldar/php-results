<?php

declare(strict_types=1);

namespace Hereldar\Results\Tests;

use Hereldar\Results\Error;
use Hereldar\Results\Ok;
use Hereldar\Results\Result;
use RuntimeException;

final class ResultTest extends TestCase
{
    /**
     * @psalm-suppress InaccessibleMethod
     */
    public function testPrivateConstructor(): void
    {
        self::assertException(
            \Error::class,
            fn () => new Result() // @phpstan-ignore-line
        );
    }

    public function testOfValue(): void
    {
        $result = Result::of(null);
        self::assertInstanceOf(Ok::class, $result);
        self::assertNull($result->value());

        $result = Result::of(false);
        self::assertInstanceOf(Ok::class, $result);
        self::assertFalse($result->value());

        $value = \fake()->integer();
        $result = Result::of($value);
        self::assertInstanceOf(Ok::class, $result);
        self::assertSame($value, $result->value());

        $value = \fake()->float();
        $result = Result::of($value);
        self::assertInstanceOf(Ok::class, $result);
        self::assertSame($value, $result->value());

        $value = \fake()->word();
        $result = Result::of($value);
        self::assertInstanceOf(Ok::class, $result);
        self::assertSame($value, $result->value());
    }

    public function testOfClosure(): void
    {
        $result = Result::of(fn () => null);
        self::assertInstanceOf(Ok::class, $result);
        self::assertNull($result->value());

        $result = Result::of(fn () => false);
        self::assertInstanceOf(Ok::class, $result);
        self::assertFalse($result->value());

        $value = \fake()->integer();
        $result = Result::of(fn () => $value);
        self::assertInstanceOf(Ok::class, $result);
        self::assertSame($value, $result->value());

        $value = \fake()->float();
        $result = Result::of(fn () => $value);
        self::assertInstanceOf(Ok::class, $result);
        self::assertSame($value, $result->value());

        $value = \fake()->word();
        $result = Result::of(fn () => $value);
        self::assertInstanceOf(Ok::class, $result);
        self::assertSame($value, $result->value());

        $exception = new RuntimeException();
        $result = Result::of(fn () => throw $exception);
        self::assertInstanceOf(Error::class, $result);
        self::assertSame($exception, $result->value());
    }

    public function testFromNullableValue(): void
    {
        $result = Result::fromNullable(null);
        self::assertInstanceOf(Error::class, $result);
        self::assertNull($result->value());

        $result = Result::fromNullable(false);
        self::assertInstanceOf(Ok::class, $result);
        self::assertFalse($result->value());

        $value = \fake()->integer();
        $result = Result::fromNullable($value);
        self::assertInstanceOf(Ok::class, $result);
        self::assertSame($value, $result->value());

        $value = \fake()->float();
        $result = Result::fromNullable($value);
        self::assertInstanceOf(Ok::class, $result);
        self::assertSame($value, $result->value());

        $value = \fake()->word();
        $result = Result::fromNullable($value);
        self::assertInstanceOf(Ok::class, $result);
        self::assertSame($value, $result->value());
    }

    public function testFromNullableClosure(): void
    {
        $result = Result::fromNullable(fn () => null);
        self::assertInstanceOf(Error::class, $result);
        self::assertNull($result->value());

        $result = Result::fromNullable(fn () => false);
        self::assertInstanceOf(Ok::class, $result);
        self::assertFalse($result->value());

        $value = \fake()->integer();
        $result = Result::fromNullable(fn () => $value);
        self::assertInstanceOf(Ok::class, $result);
        self::assertSame($value, $result->value());

        $value = \fake()->float();
        $result = Result::fromNullable(fn () => $value);
        self::assertInstanceOf(Ok::class, $result);
        self::assertSame($value, $result->value());

        $value = \fake()->word();
        $result = Result::fromNullable(fn () => $value);
        self::assertInstanceOf(Ok::class, $result);
        self::assertSame($value, $result->value());

        $exception = new RuntimeException();
        self::assertException(
            $exception,
            fn () => Result::fromNullable(fn () => throw $exception)
        );
    }

    public function testFromFalsableValue(): void
    {
        $result = Result::fromFalsable(null);
        self::assertInstanceOf(Ok::class, $result);
        self::assertNull($result->value());

        $result = Result::fromFalsable(false);
        self::assertInstanceOf(Error::class, $result);
        self::assertNull($result->value());

        $value = \fake()->integer();
        $result = Result::fromFalsable($value);
        self::assertInstanceOf(Ok::class, $result);
        self::assertSame($value, $result->value());

        $value = \fake()->float();
        $result = Result::fromFalsable($value);
        self::assertInstanceOf(Ok::class, $result);
        self::assertSame($value, $result->value());

        $value = \fake()->word();
        $result = Result::fromFalsable($value);
        self::assertInstanceOf(Ok::class, $result);
        self::assertSame($value, $result->value());
    }

    public function testFromFalsableClosure(): void
    {
        $result = Result::fromFalsable(fn () => null);
        self::assertInstanceOf(Ok::class, $result);
        self::assertNull($result->value());

        $result = Result::fromFalsable(fn () => false);
        self::assertInstanceOf(Error::class, $result);
        self::assertNull($result->value());

        $value = \fake()->integer();
        $result = Result::fromFalsable(fn () => $value);
        self::assertInstanceOf(Ok::class, $result);
        self::assertSame($value, $result->value());

        $value = \fake()->float();
        $result = Result::fromFalsable(fn () => $value);
        self::assertInstanceOf(Ok::class, $result);
        self::assertSame($value, $result->value());

        $value = \fake()->word();
        $result = Result::fromFalsable(fn () => $value);
        self::assertInstanceOf(Ok::class, $result);
        self::assertSame($value, $result->value());

        $exception = new RuntimeException();
        self::assertException(
            $exception,
            fn () => Result::fromFalsable(fn () => throw $exception)
        );
    }
}
