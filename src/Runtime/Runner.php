<?php

namespace ActTesting\Act\Runtime;

use ActTesting\Act\Console\Console;
use ActTesting\Act\Runtime\Context;
use ActTesting\Act\Steps\StepRegistry;

class Runner
{
    public function __construct(
        private Console $console,
    ) {}

    public function run(array $scenarios): void
    {
        $context = new Context();

        $successCount = 0;
        $failCount = 0;

        foreach ($scenarios as $i => $scenario) {

            $this->displayScenario($i, $scenario);

            foreach ($scenario as $desc) {

                try {

                    [$type, $callback] = StepRegistry::find($desc);

                    $typeLabel = str_pad(" $type:", 8);
                    echo $this->console->colorType($type, $typeLabel) . " $desc ... ";

                    $result = $callback($context);
                    $context->absorb($result);

                    echo $this->console->ansi("✔", '32') . "\n";

                } catch (\Throwable $e) {
                    echo $this->console->ansi("✘ " . $e->getMessage(), '31') . "\n";
                    $failCount++;
                    break;
                }
            }

            $successCount++;
        }

        $this->displayResult($successCount, $failCount);
    }

    private function displayScenario(int $i, array $scenario): void
    {
        echo "\n" . $this->console->ansi("=== Scenario " . ($i + 1) . " ===", '1;36') . "\n";
    }

    private function displayResult(int $successCount, int $failCount): void
    {
        echo "\n" . $this->console->ansi('Result:', '1')
            . ' ' . $this->console->ansi($successCount . ' OK', '32');

        if ($failCount > 0) {
            echo ', ' . $this->console->ansi($failCount . ' FAIL', '31');
        }

        echo "\n";
    }
}
