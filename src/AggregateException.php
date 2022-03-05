<?php

declare(strict_types=1);

namespace Hereldar\Results;

use Hereldar\Results\Interfaces\IAggregateException;
use Hereldar\Results\Interfaces\IResult;
use RuntimeException;

class AggregateException extends RuntimeException implements IAggregateException
{
    /**
     * @param IResult[] $results
     */
    public function __construct(
        protected readonly array $results
    ) {
        parent::__construct();
    }

    public function getErrors(): array
    {
        $errors = [];

        foreach ($this->results as $result) {
            if ($result->isError()) {
                $errors[] = $result;
            }
        }

        return $errors;
    }

    public function getResults(): array
    {
        return $this->results;
    }
}
