<?php

declare(strict_types=1);

namespace Chubbyphp\Config;

final class ConfigProvider implements ConfigProviderInterface
{
    /**
     * @var string
     */
    private $rootDir;

    /**
     * @var array<string, string>
     */
    private $configMappings = [];

    /**
     * @var array<string, ConfigInterface>
     */
    private $configs = [];

    /**
     * @param string                             $rootDir
     * @param array<int, ConfigMappingInterface> $configMappings
     */
    public function __construct(string $rootDir, array $configMappings)
    {
        $this->rootDir = $rootDir;
        foreach ($configMappings as $configMapping) {
            $this->addMapping($configMapping);
        }
    }

    public function get(string $environment): ConfigInterface
    {
        if (!isset($this->configMappings[$environment])) {
            throw ConfigException::createByEnvironment($environment);
        }

        if (!isset($this->configs[$environment])) {
            $class = $this->configMappings[$environment];

            $this->configs[$environment] = $class::create($this->rootDir);
        }

        return $this->configs[$environment];
    }

    private function addMapping(ConfigMappingInterface $configMapping): void
    {
        $this->configMappings[$configMapping->getEnvironment()] = $configMapping->getClass();
    }
}
