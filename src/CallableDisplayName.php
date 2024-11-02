<?php

namespace ICanBoogie\Console;

use Closure;
use ICanBoogie\Service\ServiceReference;
use ReflectionFunction;

use function get_debug_type;
use function implode;
use function is_array;
use function is_string;

/**
 * Returns a display name for a callable.
 */
final class CallableDisplayName
{
    public static function from(callable $callable): string
    {
        if (is_string($callable)) {
            return $callable;
        }

        if (is_array($callable)) {
            return implode('::', $callable);
        }

        if ($callable instanceof Closure) {
            $reflection = new ReflectionFunction($callable);

            return "(closure) " . $reflection->getFileName() . "@" . $reflection->getStartLine();
        }

        if ($callable instanceof ServiceReference) {
            return "(ref) $callable";
        }

        return "callable " . get_debug_type($callable);
    }
}
