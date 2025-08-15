<?php

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../src/helpers.php';

use ActTesting\Act\Console;
use ActTesting\Act\StepLoader;
use ActTesting\Act\ScenarioLoader;
use ActTesting\Act\Runner;
use ActTesting\Act\ConfigLoader;

$startTime = microtime(true);

$console = new Console(PHP_SAPI === 'cli');

$options = getopt('', ['configuration:', 'c:']);
$configFile = $options['configuration'] ?? ($options['c'] ?? null);

$config = (new ConfigLoader())->loadFrom($configFile ?? 'act.xml');

$stepLoader = new StepLoader();
$stepLoader->loadFrom($config->stepsDir);

$scenarioLoader = new ScenarioLoader();
$steps = $scenarioLoader->loadFrom($config->scenariosDir);

$runner = new Runner($console);
$runner->run($steps);

$elapsedMs = (int) round((microtime(true) - $startTime) * 1000);
echo $console->ansi('Duration:', '1')
     . ' ' . $console->ansi($elapsedMs . ' ms', '36') 
     . "\n";
