<?php

namespace Tests\Fixtures;

class Calculator
{
    public function __construct(public string $name) {}

    public function add(int $a, int $b): int
    {
        return $a + $b;
    }

    public function divide(int $a, int $b): float
    {
        if ($b === 0) {
            throw new \Exception('Division by zero');
        }

        return $a / $b;
    }
}
