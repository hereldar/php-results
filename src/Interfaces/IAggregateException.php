<?php

declare(strict_types=1);

namespace Hereldar\Results\Interfaces;

use Throwable;

interface IAggregateException extends Throwable
{
    /**
     * Returns an array with the errors that includes the aggregate
     * exception.
     *
     * @return IResult[]
     */
    public function getErrors(): array;

    /**
     * Returns an array with the results that includes the aggregate
     * exception.
     *
     * @return IResult[]
     */
    public function getResults(): array;
}