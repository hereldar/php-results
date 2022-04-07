<?php

declare(strict_types=1);

namespace Hereldar\Results\Exceptions;

use LogicException;

final class UndefinedException extends LogicException
{
    private const MESSAGE = 'Undefined exception';

    public function __construct()
    {
        parent::__construct(self::MESSAGE);
    }
}
