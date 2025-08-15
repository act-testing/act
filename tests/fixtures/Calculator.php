<?php

namespace Tests\Fixtures;

class Calculator
{
    public function __construct(public string $name) {}

    public function add(int $a, int $b): int
    {
        return $a + $b;
    }
}
