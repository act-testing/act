<?php

namespace ActTesting\Act\Steps;

class StepArgumentResolver
{
    public static function resolveArgs(callable $callback, string $pattern, array $rawArgsByName): array
    {
        $names = self::extractArgumentNamesFromStepPattern($pattern);

        $args = [];

        foreach ($names as $name) {
            /**
             * for each name in the signature, we get the raw value
             */
            $raw = $rawArgsByName[$name] ?? null;

            $args[] = $raw;
        }

        return $args;
    }

    /**
     * ex, I have a calculator named [name] => ['name']
     */
    private static function extractArgumentNamesFromStepPattern(string $pattern): array
    {
        $names = [];

        if (preg_match_all('/\[([^\]]+)\]/', $pattern, $m)) {
            $names = $m[1];
        }

        return $names;
    }
}


