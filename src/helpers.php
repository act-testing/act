<?php

namespace ActTesting\Act;

use ActTesting\Act\StepRegistry;

function given(string $description, callable $callback): void
{
    StepRegistry::register('given', $description, $callback);
}

function when(string $description, callable $callback): void
{
    StepRegistry::register('when', $description, $callback);
}

function then(string $description, callable $callback): void
{
    StepRegistry::register('then', $description, $callback);
}
