<?php

namespace ActTesting\Act\Steps;

use ActTesting\Act\Runtime\Context;

class StepRegistry
{
    public static array $steps = [];

    public static function register(string $type, string $description, callable $callback): void
    {
        $normalizedType = strtolower($type);

        self::$steps[$normalizedType] ??= [];

        self::$steps[$normalizedType][] = [
            'pattern' => $description,
            'callback' => $callback,
        ];
    }

    public static function find(string $description): array
    {
        $exactMatch = self::tryFindExactMatch($description);

        if ($exactMatch) {
            return $exactMatch;
        }

        [$type, $def, $args] = self::matchPatternAndResolveArgs($description);

        return [$type, self::createInvoker($def, $args)];
    }

    /**
     * Try to find an exact match
     *
     * @return array{0:string,1:callable}|null
     */
    private static function tryFindExactMatch(string $description): ?array
    {
        foreach (['given', 'when', 'then'] as $type) {
            foreach (self::$steps[$type] ?? [] as $def) {
                if ($def['pattern'] === $description) {
                    return [$type, $def['callback']];
                }
            }
        }

        return null;
    }

    /**
     * Match the pattern and resolve the arguments.
     *
     * @return array{0:string,1:array{pattern:string,callback:callable},2:array}
     */
    private static function matchPatternAndResolveArgs(string $description): array
    {
        [$type, $def, $rawArgs] = StepMatcher::match(self::$steps, $description);
        $args = StepArgumentResolver::resolveArgs($def['callback'], $def['pattern'], $rawArgs);

        return [$type, $def, $args];
    }

    /**
     * Build the callable to invoke by injecting `Context` followed by the resolved args.
     */
    private static function createInvoker(array $def, array $args): callable
    {
        return function (Context $ctx) use ($def, $args) {
            return ($def['callback'])(...array_merge([$ctx], $args));
        };
    }
}
