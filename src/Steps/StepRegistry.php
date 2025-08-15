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
        // Store as a list of step definitions to support pattern matching
        self::$steps[$normalizedType][] = [
            'pattern' => $description,
            'callback' => $callback,
        ];
    }

    public static function find(string $description): array
    {
        // Backward-compatible exact match first
        foreach (['given', 'when', 'then'] as $type) {
            foreach (self::$steps[$type] ?? [] as $def) {
                if ($def['pattern'] === $description) {
                    return [$type, $def['callback']];
                }
            }
        }

        // Otherwise try pattern matching on placeholders
        [$type, $callback, $args] = self::match($description);

        return [$type, function (Context $ctx) use ($callback, $args) {
            return $callback(...array_merge([$ctx], $args));
        }];
    }

    public static function match(string $description): array
    {
        foreach (['given', 'when', 'then'] as $type) {
            foreach (self::$steps[$type] ?? [] as $def) {
                $pattern = $def['pattern'];
                $regex = self::buildRegexFromPattern($pattern);

                if ($regex === null) {
                    continue;
                }

                if (preg_match($regex, $description, $matches)) {
                    $args = self::extractArguments($def['callback'], $pattern, $matches);
                    return [$type, $def['callback'], $args];
                }
            }
        }

        throw new \Exception("Step not found: $description");
    }

    private static function buildRegexFromPattern(string $pattern): ?string
    {
        // Escape everything first
        $quoted = preg_quote($pattern, '/');
        // Replace escaped placeholders like \[name\] with a named capturing group that matches bracketed values
        // It will match e.g. "[1]", "['abc']", "[true]" etc., capturing the inside content only
        $regexBody = preg_replace('/\\\\\[([^\]]+)\\\\\]/', '\\[(?P<$1>.*?)\\]', $quoted);
        if ($regexBody === null) {
            return null;
        }
        return '/^' . $regexBody . '$/i';
    }

    private static function extractArguments(callable $callback, string $pattern, array $matches): array
    {
        // Determine placeholder order from the pattern
        $names = [];
        if (preg_match_all('/\[([^\]]+)\]/', $pattern, $m)) {
            $names = $m[1];
        }

        // Reflect callback to align and coerce types
        [$paramTypes, $skipFirst] = self::reflectParameterTypes($callback);

        $args = [];
        foreach ($names as $index => $name) {
            $raw = $matches[$name] ?? null;
            $type = $paramTypes[$index + ($skipFirst ? 1 : 0)] ?? null;
            $args[] = self::coerceValue($raw, $type);
        }
        return $args;
    }

    private static function reflectParameterTypes(callable $callback): array
    {
        $reflection = null;
        if (is_array($callback)) {
            $reflection = new \ReflectionMethod($callback[0], $callback[1]);
        } elseif ($callback instanceof \Closure || is_string($callback)) {
            $reflection = new \ReflectionFunction($callback);
        } elseif (is_object($callback) && method_exists($callback, '__invoke')) {
            $reflection = new \ReflectionMethod($callback, '__invoke');
        } else {
            $reflection = new \ReflectionFunction($callback);
        }

        $params = $reflection->getParameters();
        $skipFirst = false;
        if (isset($params[0])) {
            $t = $params[0]->getType();
            if ($t instanceof \ReflectionNamedType && $t->getName() === Context::class) {
                $skipFirst = true;
            }
        }

        $types = [];
        foreach ($params as $p) {
            $types[] = $p->getType();
        }

        return [$types, $skipFirst];
    }

    private static function coerceValue(?string $raw, ?\ReflectionType $type)
    {
        if ($raw === null) {
            return null;
        }

        $raw = trim($raw);

        // Remove surrounding quotes if present
        if ((str_starts_with($raw, "'") && str_ends_with($raw, "'")) || (str_starts_with($raw, '"') && str_ends_with($raw, '"'))) {
            $raw = substr($raw, 1, -1);
        }

        $typeName = null;
        if ($type instanceof \ReflectionNamedType) {
            $typeName = $type->getName();
        }

        // Explicit type coercion if requested by signature
        switch ($typeName) {
            case 'int':
            case 'integer':
                return (int) $raw;
            case 'float':
            case 'double':
                return (float) $raw;
            case 'bool':
            case 'boolean':
                $v = strtolower($raw);
                return in_array($v, ['1', 'true', 'yes', 'on'], true);
            case 'string':
                return (string) $raw;
            default:
                // Best-effort inference when no explicit type provided
                if (preg_match('/^-?\d+$/', $raw)) {
                    return (int) $raw;
                }
                if (preg_match('/^-?\d*\.\d+$/', $raw)) {
                    return (float) $raw;
                }
                $v = strtolower($raw);
                if (in_array($v, ['true', 'false'], true)) {
                    return $v === 'true';
                }
                // Try JSON if looks like JSON
                $trim = ltrim($raw);
                if ($trim !== '' && ($trim[0] === '{' || $trim[0] === '[')) {
                    $decoded = json_decode($raw, true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        return $decoded;
                    }
                }
                return $raw;
        }
    }
}
