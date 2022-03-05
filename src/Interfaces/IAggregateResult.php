<?php

declare(strict_types=1);

namespace Hereldar\Results\Interfaces;

interface IAggregateResult extends IResult
{
    /**
     * Returns an array with the errors that includes the aggregate
     * result.
     *
     * @return IResult[]
     */
    public function individualErrors(): array;

    /**
     * Returns an array with the individual results that compose the
     * aggregate result.
     *
     * @return IResult[]
     */
    public function individualResults(): array;

    /**
     * Returns `true` if the aggregate result does not contain any
     * individual results.
     *
     * @return bool
     */
    public function isEmpty(): bool;
}
