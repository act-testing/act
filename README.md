# Act — Minimal BDD testing framework for PHP

Act is a tiny BDD-inspired testing framework (Given/When/Then). It lets you write readable scenarios in natural language and bind them to simple PHP step functions.

## Key features

- Steps declared with `given`, `when`, `then`
- Scenarios are plain PHP arrays (no complex DSL)
- Named parameters via square-bracket placeholders, e.g. `I add [a] and [b]`
- Shared execution context (`Context`) across steps
- Minimal assertions included (`Assert::true`, `Assert::false`, `Assert::equals`, `Assert::contains`, `Assert::fail`)
- Clean console output with OK/FAIL counts

## Installation

Install via Composer (if published on Packagist):

```bash
composer require act-testing/act --dev
```

Or use it locally (e.g. cloned repository) and add a `path` repository entry in your `composer.json`.

## Configuration (act.xml)

You can define the paths for steps and scenarios via an `act.xml` file at your project root.

Example:

```xml
<?xml version="1.0" encoding="UTF-8"?>
<act>
    <paths>
        <steps>tests/steps</steps>
        <scenarios>tests/scenarios</scenarios>
    </paths>
</act>
```

Defaults if `act.xml` is missing/incomplete:

- steps: `tests/steps`
- scenarios: `tests/scenarios`

## Writing steps

Declare your steps in PHP files scanned by Act (under the `steps` path). Use the `given`, `when`, `then` functions and the shared `Context`:

```php
<?php

use ActTesting\Act\Assert\Assert;
use ActTesting\Act\Runtime\Context;
use function ActTesting\Act\Steps\then;
use function ActTesting\Act\Steps\when;
use function ActTesting\Act\Steps\given;

given('I have a calculator named [name]', function (Context $test, string $name) {
    $test->calculator = new Calculator($name);
});

when('I add [a] and [b]', function (Context $test, int $a, int $b) {
    $test->result = $test->calculator->add($a, $b);
});

then('I get [result]', function (Context $test, int $result) {
    Assert::equals($result, $test->result);
});
```

### Placeholders and argument resolution

- In step patterns, parameters are wrapped in square brackets: `[name]`, `[a]`, `[b]`, ...
- The matcher extracts these values from the scenario line and passes them to your callback in the order they appear in the pattern.
- You may type-hint your parameters (e.g. `int $a`). Without `declare(strict_types=1)`, PHP will attempt implicit coercion where possible.

## Writing scenarios

A scenario file returns an array of scenarios. Each scenario is an ordered list of step descriptions:

```php
<?php

return [
    [
        'I have a calculator named [hal9000]',
        'I add [1] and [2]',
        'I get [3]',
    ],
];
```

## Command line

Run Act with a specific configuration file:

```bash
php bin/act.php --configuration=act.xml
```

Supported aliases: `--configuration`, `-c`.

### Example output

```
=== Scenario 1 ===
  given: I have a calculator named [hal9000] ... ✔
   when: I add [1] and [2] ... ✔
   then: I get [3] ... ✔

Result: 1 OK
Duration: 12 ms
```

## Concepts and API

### Execution context (`Context`)

Each scenario runs with a `Context` shared across steps:

- Store values: `$test->calculator = ...;`, `$test->set('key', $value);`
- Read values: `$test->calculator`, `$test->getValue('key')`
- A step may return a value; it is absorbed into the context:
  - `array` or `stdClass` → merged into the context
  - `Context` → keys merged
  - other scalar → stored under `lastResult`

### Assertions

Available via `ActTesting\Act\Assert\Assert`:

- `Assert::true($cond, ?$message = null)`
- `Assert::false($cond, ?$message = null)`
- `Assert::equals($expected, $actual, ?$message = null)`
- `Assert::contains($needle, $haystack, ?$message = null)`
- `Assert::fail($message)`
