<?php

declare(strict_types=1);

namespace Hereldar\Results;

use Hereldar\Results\Exceptions\AggregateException;
use Hereldar\Results\Interfaces\IAggregateException;
use Hereldar\Results\Interfaces\IAggregateResult;
use Hereldar\Results\Interfaces\IResult;

/**
 * @extends AbstractResult<null>
 */
class AggregateResult extends AbstractResult implements IAggregateResult
{
    /** @var IResult[] */
    protected readonly array $individualResults;
    protected readonly bool $isError;

    public function __construct(IResult ...$results)
    {
        $this->individualResults = $results;
        $this->isError = (bool) $this->countIndividualErrors();

        parent::__construct();
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

    public function isError(): bool
    {
        $this->used = true;

        return $this->isError;
    }

    public function isOk(): bool
    {
        $this->used = true;

        return !$this->isError;
    }

    /**
     * @throws IAggregateException
     */
    public function orFail(): mixed
    {
        $this->used = true;

        if ($this->isError()) {
            throw $this->aggregateException();
        }

        return null;
    }

    protected function aggregateException(): IAggregateException
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
