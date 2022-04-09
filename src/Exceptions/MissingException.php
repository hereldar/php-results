<?php

declare(strict_types=1);

namespace Hereldar\Results\Exceptions;

use Hereldar\Results\Interfaces\IResult;
use LogicException;

final class MissingException extends LogicException
{
    private const MESSAGE = 'Error `%s` must provide an exception';

    public function __construct(
        IResult $result,
    ) {
        parent::__construct(sprintf(self::MESSAGE, $result::class));
    }
}
