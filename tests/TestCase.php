<?php

declare(strict_types=1);

namespace Hereldar\Results\Tests;

use PHPUnit\Framework\Constraint\Exception as ExceptionConstraint;
use PHPUnit\Framework\Constraint\IsIdentical;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use Throwable;

abstract class TestCase extends PHPUnitTestCase
{
    /**
     * @param class-string<Throwable> $expectedException
     */
    public static function assertException(
        string $expectedException,
        callable $callback
    ): void {
        try {
            $callback();
        } catch (Throwable $exception) {
        }
        static::assertThat(
            $exception ?? null,
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
        } catch (Throwable $exception) {
        }
        static::assertThat(
            $exception?->getMessage(),
            new IsIdentical(
                $expectedMessage
            )
        );
    }
}
