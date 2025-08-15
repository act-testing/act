<?php

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../src/helpers.php';

use ActTesting\Act\Console;
use ActTesting\Act\StepLoader;
use ActTesting\Act\ScenarioLoader;
use ActTesting\Act\Runner;

$startTime = microtime(true);

$console = new Console(PHP_SAPI === 'cli');

$stepLoader = new StepLoader();
$stepLoader->loadFrom(__DIR__ . '/../tests/steps');

$scenarioLoader = new ScenarioLoader();
$steps = $scenarioLoader->loadFrom(__DIR__ . '/../tests/scenarios');

$runner = new Runner($console);
$runner->run($steps);

$elapsedMs = (int) round((microtime(true) - $startTime) * 1000);
echo $console->ansi('Duration:', '1')
     . ' ' . $console->ansi($elapsedMs . ' ms', '36') 
     . "\n";
