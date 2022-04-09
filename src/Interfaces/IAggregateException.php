<?php

declare(strict_types=1);

namespace Hereldar\Results\Interfaces;

use Throwable;

interface IAggregateException extends Throwable
{
    /**
     * @param IResult[] $results
     *
     * @psalm-param list<IResult> $results
     */
    public function __construct(array $results);

    /**
     * Returns an array with the errors that includes the aggregate
     * exception.
     *
     * @return IResult[]
     *
     * @psalm-return list<IResult>
     */
    public function getErrors(): array;

    /**
     * Returns an array with the results that includes the aggregate
     * exception.
     *
     * @return IResult[]
     *
     * @psalm-return list<IResult>
     */
    public function getResults(): array;
}
