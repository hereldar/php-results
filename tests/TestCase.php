<?php

declare(strict_types=1);

namespace Hereldar\Results\Tests;

use Faker\Factory as FakerFactory;
use Faker\Generator as FakerGenerator;
use PHPUnit\Framework\Constraint\Exception as ExceptionConstraint;
use PHPUnit\Framework\Constraint\IsIdentical;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use Throwable;

abstract class TestCase extends PHPUnitTestCase
{
    private FakerGenerator|null $random = null;

    /**
     * @param class-string<Throwable> $expectedException
     *
     * @psalm-suppress InternalClass
     * @psalm-suppress InternalMethod
     */
    public static function assertException(
        string $expectedException,
        callable $callback
    ): void {
        try {
            $callback();
            $exception = null;
        } catch (Throwable $exception) {
        }
        /** @psalm-suppress PossiblyUndefinedVariable */
        static::assertThat(
            $exception,
            new ExceptionConstraint(
                $expectedException
            )
        );
    }

    public static function assertExceptionMessage(
        string $expectedMessage,
        callable $callback
    ): void {
        try {
            $callback();
            $message = null;
        } catch (Throwable $exception) {
            $message = $exception->getMessage();
        }
        static::assertThat(
            $message,
            new IsIdentical(
                $expectedMessage
            )
        );
    }

    protected function random(): FakerGenerator
    {
        return $this->random ??= FakerFactory::create();
    }
}
