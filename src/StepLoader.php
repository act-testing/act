<?php

namespace ActTesting\Act;

class StepLoader extends Loader
{
    public function loadFrom(string $stepsDir): void
    {
        $this->checkDirIsAccessible($stepsDir);

        $directoryIterator = $this->loadDir($stepsDir);

        foreach ($directoryIterator as $fileInfo) {
            
            if ($this->isPhpFile($fileInfo)) {
                require_once $fileInfo->getPathname();
            }
        }
    }
}
