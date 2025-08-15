<?php

namespace ActTesting\Act\Console;

class TerminalConsole
{
    public function __construct(
        private bool $enableColors = false,
    ) {}

    public function ansi(string $text, string $code): string
    {
        if (! $this->enableColors) {
            return $text;
        }

        return "\033[{$code}m{$text}\033[0m";
    }

    public function colorType(string $type, string $label): string
    {
        $map = [
            'given' => '36',
            'when'  => '33',
            'then'  => '35',
        ];

        $code = $map[strtolower($type)] ?? '0';
        
        return $this->ansi($label, $code);
    }

    public function write(string $text): void
    {
        echo $text;
    }

    public function writeln(string $text = ''): void
    {
        echo $text . "\n";
    }
}


