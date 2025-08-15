<?php

namespace ActTesting\Act\Loading;

use SplFileInfo;

class ScenarioLoader extends Loader
{
    public function loadFrom(string $scenariosDir): array
    {
        $this->checkDirIsAccessible($scenariosDir);

        $directoryIterator = $this->loadDir($scenariosDir);

        $steps = [];

        foreach ($directoryIterator as $fileInfo) {

            if ($this->isPhpFile($fileInfo)) {
                $steps = array_merge($steps, $this->getFileScenarios($fileInfo));
            }
        }

        return $steps;
    }

    private function getFileScenarios(SplFileInfo $file): array
    {
        return require $file->getPathname();
    }
}
