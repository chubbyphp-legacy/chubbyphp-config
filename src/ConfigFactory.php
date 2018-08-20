<?php

declare(strict_types=1);

namespace Chubbyphp\Config;

final class ConfigFactory implements ConfigFactoryInterface
{
    /**
     * @var ConfigMappingInterface[]
     */
    private $configMappings;

    /**
     * @param ConfigMappingInterface[] $configMappings
     */
    public function __construct(array $configMappings)
    {
        $this->configMappings = [];
        foreach ($configMappings as $configMapping) {
            $this->addMapping($configMapping);
        }
    }

    /**
     * @param ConfigMappingInterface $configMapping
     */
    private function addMapping(ConfigMappingInterface $configMapping)
    {
        $this->configMappings[$configMapping->getEnvironment()] = $configMapping->getClass();
    }

    /**
     * @param string $rootDir
     * @param string $environment
     *
     * @return ConfigInterface
     *
     * @throws ConfigException
     */
    public function create(string $rootDir, string $environment): ConfigInterface
    {
        if (!isset($this->configMappings[$environment])) {
            throw ConfigException::createByEnvironment($environment);
        }

        $class = $this->configMappings[$environment];

        return new $class($rootDir);
    }
}
