<?php

declare(strict_types=1);

namespace Hereldar\Results;

/**
 * @internal
 *
 * @psalm-suppress all
 */
final class Backtrace
{
    private readonly array $trace;

    /**
     * @param class-string $class
     */
    public function __construct(string $class = self::class)
    {
        $trace = debug_backtrace(
            options: DEBUG_BACKTRACE_IGNORE_ARGS,
            limit: 15
        );

        $lastIndex = null;

        foreach ($trace as $i => $call) {
            if ($class === ($call['class'] ?? '')) {
                $lastIndex = $i;
            }
        }

        if ($lastIndex !== null) {
            $trace = array_slice($trace, $lastIndex, 5);
        }

        $this->trace = $trace;
    }

    public function __toString(): string
    {
        $string = '';

        foreach ($this->trace as $i => $call) {
            $string .= "#{$i} ";

            if (isset($call['file'])) {
                $string .= $call['file'];

                if (isset($call['line'])) {
                    $string .= "({$call['line']})";
                }
                $string .= ': ';
            }

            if (isset($call['class'])) {
                $string .= "{$call['class']}::";
            }

            $string .= "{$call['function']}()\n";
        }

        return $string;
    }
}
