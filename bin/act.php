<?php

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../src/Steps/helpers.php';

use ActTesting\Act\Console\Console;
use ActTesting\Act\Console\TerminalConsole;
use ActTesting\Act\Loading\StepLoader;
use ActTesting\Act\Loading\ScenarioLoader;
use ActTesting\Act\Runtime\Runner;
use ActTesting\Act\Config\ConfigLoader;

$startTime = microtime(true);

$terminal = new TerminalConsole(PHP_SAPI === 'cli');
$console = new Console($terminal);

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
$console->writeln($console->bold('Duration:') . ' ' . $console->info($elapsedMs . ' ms'));
