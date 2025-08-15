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
        $successCount = 0;
        $failCount = 0;

        foreach ($scenarios as $i => $scenario) {

            $this->displayScenario($i, $scenario);

            $context = new Context();

            $scenarioFailed = false;
            $exceptionCaught = false;

            foreach ($scenario as $desc) {

                try {

                    [$type, $callback] = StepRegistry::find($desc);

                    $this->console->displayStepStart($type, $desc);

                    $result = $callback($context);
                    $context->absorb($result);

                    $this->console->displayStepSuccess();

                } catch (\Throwable $e) {
                    $context->exception = $e;
                    $exceptionCaught = true;
                    $this->console->displayStepException($e);
                }
            }

            if ($exceptionCaught && $context->has('exception')) {
                $scenarioFailed = true;
                $this->console->displayScenarioExceptionNotValidated();
            }

            $scenarioFailed ? $failCount++ : $successCount++;
        }

        $this->displayResult($successCount, $failCount);
    }

    private function displayScenario(int $i, array $scenario): void
    {
        $this->console->displayScenarioHeader($i);
    }

    private function displayResult(int $successCount, int $failCount): void
    {
        $this->console->displayResult($successCount, $failCount);
    }
}
