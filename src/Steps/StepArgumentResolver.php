<?php

namespace ActTesting\Act\Steps;

use ActTesting\Act\Runtime\Context;

class StepArgumentResolver
{
    public static function resolveArgs(callable $callback, string $pattern, array $rawArgsByName): array
    {
        [$types, $skipFirst] = self::reflectParameterTypes($callback);

        $names = [];
        if (preg_match_all('/\[([^\]]+)\]/', $pattern, $m)) {
            $names = $m[1];
        }

        $args = [];

        foreach ($names as $index => $name) {
            $raw = $rawArgsByName[$name] ?? null;
            $type = $types[$index + ($skipFirst ? 1 : 0)] ?? null;
            $args[] = self::coerceValue($raw, $type);
        }

        return $args;
    }

    private static function reflectParameterTypes(callable $callback): array
    {
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

        if ((str_starts_with($raw, "'") && str_ends_with($raw, "'")) || (str_starts_with($raw, '"') && str_ends_with($raw, '"'))) {
            $raw = substr($raw, 1, -1);
        }

        $typeName = null;
        if ($type instanceof \ReflectionNamedType) {
            $typeName = $type->getName();
        }

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


