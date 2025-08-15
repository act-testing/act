<?php

namespace ActTesting\Act\Runtime;

class Context
{
    private array $bag = [];

    public function __get($name)
    {
        return $this->bag[$name] ?? null;
    }

    public function __set($name, $value): void
    {
        $this->bag[$name] = $value;
    }

    public function __isset($name): bool
    {
        return isset($this->bag[$name]);
    }

    public function __unset($name): void
    {
        unset($this->bag[$name]);
    }

    public function set(string $key, $value): void
    {
        $this->bag[$key] = $value;
    }

    public function getValue(string $key, $default = null)
    {
        return $this->bag[$key] ?? $default;
    }

    public function has(string $key): bool
    {
        return array_key_exists($key, $this->bag);
    }

    public function remove(string $key): void
    {
        unset($this->bag[$key]);
    }

    public function all(): array
    {
        return $this->bag;
    }

    public function absorb($value): void
    {
        if ($value === null) {
            return;
        }

        if ($value instanceof self) {
            $this->bag = array_merge($this->bag, $value->all());
            return;
        }

        if ($value instanceof \stdClass) {
            $this->bag = array_merge($this->bag, (array) $value);
            return;
        }

        if (is_array($value)) {
            $this->bag = array_merge($this->bag, $value);
            return;
        }
        
        $this->bag['lastResult'] = $value;
    }
}
