<?php

declare(strict_types=1);

namespace Hereldar\Results\Interfaces;

/**
 * @template T
 * @template E of IAggregateException
 *
 * @extends IResult<T, E>
 */
interface IAggregateResult extends IResult
{
    public function __construct(IResult ...$results);

    /**
     * Returns an array with the errors that includes the aggregate
     * result.
     *
     * @return IResult[]
     *
     * @psalm-return list<IResult>
     */
    public function individualErrors(): array;

    /**
     * Returns an array with the individual results that compose the
     * aggregate result.
     *
     * @return IResult[]
     *
     * @psalm-return list<IResult>
     */
    public function individualResults(): array;

    /**
     * Returns `true` if the aggregate result does not contain any
     * individual results.
     */
    public function isEmpty(): bool;
}
