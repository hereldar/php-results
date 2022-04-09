<?php

declare(strict_types=1);

namespace Hereldar\Results\Tests;

use PHPUnit\Framework\Constraint\Exception as ExceptionConstraint;
use PHPUnit\Framework\Constraint\ExceptionCode;
use PHPUnit\Framework\Constraint\ExceptionMessage;
use PHPUnit\Framework\Constraint\ExceptionMessageRegularExpression;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use Throwable;

abstract class TestCase extends PHPUnitTestCase
{
    /**
     * @param class-string<Throwable> $expectedException
     */
    public function assertException(
        string $expectedException,
        callable $callback
    ) {
        try {
            $callback();
        } catch (Throwable $exception) {
            $this->assertThat(
                $exception,
                new ExceptionConstraint(
                    $expectedException
                )
            );

            return;
        }

        $this->assertThat(
            null,
            new ExceptionConstraint(
                $expectedException
            )
        );
    }

    public function assertExceptionCode(
        int|string $expectedCode,
        callable $callback
    ) {
        try {
            $callback();
        } catch (Throwable $exception) {
        }

        $this->assertThat(
            $exception ?? null,
            new ExceptionCode(
                $expectedCode
            )
        );
    }

    public function assertExceptionMessage(
        string $expectedMessage,
        callable $callback
    ) {
        try {
            $callback();
        } catch (Throwable $exception) {
            return;
        }

        $this->assertThat(
            $exception ?? null,
            new ExceptionMessage(
                $expectedMessage
            )
        );
    }

    public function assertExceptionMessageMatches(
        string $regularExpression,
        callable $callback
    ) {
        try {
            $callback();
        } catch (Throwable $exception) {
        }

        $this->assertThat(
            $exception ?? null,
            new ExceptionMessageRegularExpression(
                $regularExpression
            )
        );
    }
}
