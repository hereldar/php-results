<?php

declare(strict_types=1);

namespace Hereldar\Results\Exceptions;

use Hereldar\Results\Interfaces\IResult;
use LogicException;

final class UnusedResult extends LogicException
{
    private const SHORT_MESSAGE = 'Result `%s` must be used';
    private const LONG_MESSAGE = "Result `%s` must be used\n%s";

    public function __construct(
        private readonly IResult $result,
        ?string $trace = null,
    ) {
        parent::__construct(
            (null === $trace)
                ? sprintf(self::SHORT_MESSAGE, $result::class)
                : sprintf(self::LONG_MESSAGE, $result::class, $trace)
        );
    }

    public function getResult(): IResult
    {
        return $this->result;
    }
}
