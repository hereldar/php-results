<?php

declare(strict_types=1);

namespace Hereldar\Results;

/**
 * @internal
 */
final class Backtrace
{
    /**
     * @var array[]
     *
     * @psalm-var list<array{file: string, line: int, class?: class-string, function: string}>
     */
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
            if (($call['class'] ?? '') === $class) {
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

            if (isset($call['function'])) {
                $string .= "{$call['function']}()";
            }

            $string .= "\n";
        }

        return $string;
    }
}
