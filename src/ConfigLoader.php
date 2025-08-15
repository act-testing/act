<?php

namespace ActTesting\Act;

class ConfigLoader
{
    public function loadFrom(string $configPath): Config
    {
        $realConfigPath = $this->resolveConfigPath($configPath);
        $this->assertConfigFileExists($realConfigPath, $configPath);

        $xml = $this->loadXml($realConfigPath);
        $baseDir = dirname($realConfigPath);

        [$steps, $scenarios] = $this->extractPathsFromXml($xml);
        [$stepsDir, $scenariosDir] = $this->computeDirectories($baseDir, $steps, $scenarios);

        return new Config($baseDir, $stepsDir, $scenariosDir);
    }

    private function resolveConfigPath(string $configPath): string
    {
        return realpath($configPath) ?: $configPath;
    }

    private function assertConfigFileExists(string $realConfigPath, string $originalPath): void
    {
        if (! is_file($realConfigPath)) {
            throw new \RuntimeException("Config file not found: $originalPath");
        }
    }

    private function loadXml(string $realConfigPath): \SimpleXMLElement
    {
        $xml = @simplexml_load_file($realConfigPath);

        if ($xml === false) {
            throw new \RuntimeException("Cannot parse act.xml at $realConfigPath");
        }

        return $xml;
    }

    /**
     * @return array{0: string, 1: string} [steps, scenarios]
     */
    private function extractPathsFromXml(\SimpleXMLElement $xml): array
    {
        $steps = '';
        $scenarios = '';

        if (isset($xml->paths)) {
            $steps = (string) ($xml->paths->steps ?? '');
            $scenarios = (string) ($xml->paths->scenarios ?? '');
        }

        return [$steps, $scenarios];
    }

    /**
     * @return array{0: string, 1: string} [stepsDir, scenariosDir]
     */
    private function computeDirectories(string $baseDir, string $steps, string $scenarios): array
    {
        $stepsDir = $this->resolvePath($baseDir, $steps !== '' ? $steps : 'tests/steps');
        $scenariosDir = $this->resolvePath($baseDir, $scenarios !== '' ? $scenarios : 'tests/scenarios');

        return [$stepsDir, $scenariosDir];
    }

    private function resolvePath(string $baseDir, string $path): string
    {
        if ($path === '') {
            return $baseDir;
        }

        if (str_starts_with($path, DIRECTORY_SEPARATOR)) {
            return $path;
        }

        if (preg_match('/^[A-Za-z]:\\\\/', $path) === 1) {
            return $path;
        }

        return realpath($baseDir . DIRECTORY_SEPARATOR . $path) ?: ($baseDir . DIRECTORY_SEPARATOR . $path);
    }
}


