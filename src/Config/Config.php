<?php

namespace ActTesting\Act\Config;

class Config
{
    public function __construct(
        public string $rootDir,
        public string $stepsDir,
        public string $scenariosDir,
    ) {}
}
