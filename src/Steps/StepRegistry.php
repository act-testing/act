<?php

namespace ActTesting\Act\Steps;

class StepRegistry
{
    public static array $steps = [];

    public static function register(string $type, string $description, callable $callback): void
    {
        self::$steps[strtolower($type)][$description] = $callback;
    }

    public static function find(string $description): array
    {
        foreach (['given', 'when', 'then'] as $type) {

            if (self::stepExists($type, $description)) {
                return [$type, self::$steps[$type][$description]];
            }
        }

        throw new \Exception("Step not found: $description");
    }

    private static function stepExists(string $type, string $description): bool
    {
        return isset(self::$steps[$type][$description]);
    }
}
