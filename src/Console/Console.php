<?php

namespace ActTesting\Act\Console;

class Console
{
    public function __construct(
        private TerminalConsole $terminal,
    ) {}

    public function color(string $text, string $code): string
    {
        return $this->terminal->ansi($text, $code);
    }

    public function successMark(): string
    {
        return $this->successText('✔');
    }

    public function pendingWithMessage(string $message): string
    {
        return $this->terminal->ansi('⏳ ' . $message, '33');
    }

    public function bold(string $text): string
    {
        return $this->terminal->ansi($text, '1');
    }

    public function info(string $text): string
    {
        return $this->terminal->ansi($text, '36');
    }

    public function successText(string $text): string
    {
        return $this->terminal->ansi($text, '32');
    }

    public function errorText(string $text): string
    {
        return $this->terminal->ansi($text, '31');
    }

    public function warningText(string $text): string
    {
        return $this->terminal->ansi($text, '33');
    }

    public function colorType(string $type, string $label): string
    {
        return $this->terminal->colorType($type, $label);
    }

    public function write(string $text): void
    {
        $this->terminal->write($text);
    }

    public function writeln(string $text = ''): void
    {
        $this->terminal->writeln($text);
    }

    public function displayScenarioHeader(int $index): void
    {
        $this->writeln("\n" . $this->bold($this->info("=== Scenario " . ($index + 1) . " ===")));
    }

    public function displayStepStart(string $type, string $description): void
    {
        $typeLabel = str_pad(" $type:", 8);
        $this->write($this->colorType($type, $typeLabel) . " $description ... ");
    }

    public function displayStepSuccess(): void
    {
        $this->writeln($this->successMark());
    }

    public function displayStepException(\Throwable $exception): void
    {
        $this->writeln($this->pendingWithMessage($exception->getMessage()));
    }

    public function displayScenarioExceptionNotValidated(): void
    {
        $this->writeln($this->pendingWithMessage('Exception was not validated by a THEN step'));
    }

    public function displayResult(int $successCount, int $failCount): void
    {
        $this->write("\n" . $this->bold('Result:')
            . ' ' . $this->successText($successCount . ' OK'));

        if ($failCount > 0) {
            $this->write(', ' . $this->errorText($failCount . ' FAIL'));
        }

        $this->writeln();
    }
}
