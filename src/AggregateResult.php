<?php

declare(strict_types=1);

namespace Hereldar\Results;

use Hereldar\Results\Exceptions\AggregateException;
use Hereldar\Results\Interfaces\IAggregateException;
use Hereldar\Results\Interfaces\IAggregateResult;
use Hereldar\Results\Interfaces\IResult;

/**
 * @extends AbstractResult<null, IAggregateException>
 *
 * @implements IAggregateResult<null, IAggregateException>
 */
class AggregateResult extends AbstractResult implements IAggregateResult
{
    /**
     * @var IResult[]
     *
     * @psalm-var list<IResult>
     */
    protected readonly array $individualResults;

    public function __construct(IResult ...$results)
    {
        $this->individualResults = array_values($results);

        parent::__construct(
            exception: ($this->countIndividualErrors())
                ? $this->aggregateException()
                : null,
        );
    }

    public static function empty(): static
    {
        return new static();
    }

    public static function of(IResult ...$results): static
    {
        return new static(...$results);
    }

    public function individualErrors(): array
    {
        $this->used = true;

        $errors = [];

        foreach ($this->individualResults as $result) {
            if ($result->isError()) {
                $errors[] = $result;
            }
        }

        return $errors;
    }

    public function individualResults(): array
    {
        $this->used = true;

        return $this->individualResults;
    }

    public function isEmpty(): bool
    {
        $this->used = true;

        return !$this->individualResults;
    }

    private function aggregateException(): IAggregateException
    {
        return new AggregateException($this->individualResults());
    }

    private function countIndividualErrors(): int
    {
        $count = 0;

        // We could return after the first error, but this way we mark
        // all individual results as used.
        foreach ($this->individualResults as $result) {
            if ($result->isError()) {
                ++$count;
            }
        }

        return $count;
    }
}
