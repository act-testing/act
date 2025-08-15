<?php

namespace ActTesting\Act\Steps;

use ActTesting\Act\Steps\StepException;

class StepMatcher
{
    /**
     * @param array $stepsByType [type => [['pattern' => string, 'callback' => callable], ...]]
     * @return array [$type, $definition, $rawArgsByName]
     */
    public static function match(array $stepsByType, string $description): array
    {
        foreach (['given', 'when', 'then'] as $type) {

            foreach ($stepsByType[$type] ?? [] as $def) {

                $matches = self::matchPattern($def['pattern'], $description);

                if ($matches) {

                    $rawArgs = self::extractRawArguments($def['pattern'], $matches);

                    return [$type, $def, $rawArgs];
                }
            }
        }

        throw new StepException("Step not found: $description");
    }

    private static function matchPattern(string $pattern, string $description): array
    {
        $regex = self::buildRegexFromPattern($pattern);

        $matches = [];

        preg_match($regex, $description, $matches);

        return $matches;
    }

    private static function buildRegexFromPattern(string $pattern): ?string
    {
        $quoted = preg_quote($pattern, '/');

        $regexBody = preg_replace('/\\\\\[([^\]]+)\\\\\]/', '\\[(?P<$1>.*?)\\]', $quoted);

        return '/^' . $regexBody . '$/i';
    }

    private static function extractRawArguments(string $pattern, array $matches): array
    {
        $names = [];

        if (preg_match_all('/\[([^\]]+)\]/', $pattern, $m)) {
            $names = $m[1];
        }

        $raw = [];
        
        foreach ($names as $name) {
            $raw[$name] = $matches[$name] ?? null;
        }

        return $raw;
    }
}
