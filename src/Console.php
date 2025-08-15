<?php

namespace ActTesting\Act;

class Console
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
}
