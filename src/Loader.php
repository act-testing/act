<?php

namespace ActTesting\Act;

use SplFileInfo;

class Loader
{
    protected function checkDirIsAccessible(string $scenariosDir): void
    {
        if (! is_dir($scenariosDir)) {
            throw new \Exception("Directory $scenariosDir is not accessible");
        }
    }

    /**
     * SKIP_DOTS is used to skip the . and .. directories
     */
    protected function loadDir(string $scenariosDir): \RecursiveIteratorIterator
    {
        return new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($scenariosDir, \FilesystemIterator::SKIP_DOTS)
        );
    }

    protected function isPhpFile(SplFileInfo $file): bool
    {
        return $file->isFile() && strtolower($file->getExtension()) === 'php';
    }
}
