<?php

declare(strict_types=1);

namespace Chubbyphp\Config;

final class ConfigProvider implements ConfigProviderInterface
{
    /**
     * @var array<string, ConfigInterface>
     */
    private $configs;

    /**
     * @param array<int, ConfigInterface> $configs
     */
    public function __construct(array $configs)
    {
        $this->configs = [];
        foreach ($configs as $config) {
            $this->addConfig($config);
        }
    }

    public function get(string $environment): ConfigInterface
    {
        if (!isset($this->configs[$environment])) {
            throw ConfigException::createByEnvironment($environment);
        }

        return $this->configs[$environment];
    }

    private function addConfig(ConfigInterface $config): void
    {
        $this->configs[$config->getEnvironment()] = $config;
    }
}
