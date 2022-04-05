<?php

declare(strict_types=1);

namespace Hereldar\Results\Exceptions;

use Hereldar\Results\Interfaces\IResult;
use LogicException;

final class UnusedResult extends LogicException
{
    private const SHORT_MESSAGE = 'Unused `%s` that must be used';
    private const LONG_MESSAGE = "Unused `%s` that must be used\n%s";

    public function __construct(
        private IResult $result,
        private ?string $trace = null,
    ) {
        parent::__construct(
            (null === $this->trace)
                ? sprintf(self::SHORT_MESSAGE, $result::class)
                : sprintf(self::LONG_MESSAGE, $result::class, $this->trace)
        );
    }

    public function getResult(): IResult
    {
        return $this->result;
    }
}
