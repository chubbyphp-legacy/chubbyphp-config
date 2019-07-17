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
     * @var string[]
     */
    private $configMappings = [];

    /**
     * @var ConfigInterface[]
     */
    private $configs = [];

    /**
     * @param string                   $rootDir
     * @param ConfigMappingInterface[] $configMappings
     */
    public function __construct(string $rootDir, array $configMappings)
    {
        $this->rootDir = $rootDir;
        foreach ($configMappings as $configMapping) {
            $this->addMapping($configMapping);
        }
    }

    /**
     * @param string $environment
     *
     * @throws ConfigException
     *
     * @return ConfigInterface
     */
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

    /**
     * @param ConfigMappingInterface $configMapping
     */
    private function addMapping(ConfigMappingInterface $configMapping)
    {
        $this->configMappings[$configMapping->getEnvironment()] = $configMapping->getClass();
    }
}
