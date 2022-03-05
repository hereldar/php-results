<?php

declare(strict_types=1);

namespace Hereldar\Results;

use Hereldar\Results\Interfaces\IAggregateException;
use Hereldar\Results\Interfaces\IAggregateResult;
use Hereldar\Results\Interfaces\IResult;

class AggregateResult extends AbstractResult implements IAggregateResult
{
    /** @var IResult[] */
    protected readonly array $individualResults;
    protected readonly bool $isError;

    public function __construct(IResult ...$results)
    {
        $this->individualResults = $results;
        $this->isError = $this->isAnyResultAnError();

        parent::__construct();
    }

    private function isAnyResultAnError(): bool
    {
        foreach ($this->individualResults as $result) {
            if ($result->isError()) {
                return true;
            }
        }

        return false;
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
        return $this->individualResults;
    }

    public function isEmpty(): bool
    {
        return !$this->individualResults;
    }

    public function isError(): bool
    {
        return $this->isError;
    }

    public function isOk(): bool
    {
        return !$this->isError;
    }

    /**
     * @throws IAggregateException
     */
    public function orFail(): mixed
    {
        if ($this->isError()) {
            throw $this->aggregateException();
        }

        return null;
    }

    protected function aggregateException(): IAggregateException
    {
        return new AggregateException($this->individualResults());
    }
}
