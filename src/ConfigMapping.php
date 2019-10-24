<?php

declare(strict_types=1);

namespace Chubbyphp\Config;

final class ConfigMapping implements ConfigMappingInterface
{
    /**
     * @var string
     */
    private $environment;

    /**
     * @var string
     */
    private $class;

    public function __construct(string $environment, string $class)
    {
        if (!in_array(ConfigInterface::class, class_implements($class), true)) {
            throw ConfigException::createByMissingInterface($class, ConfigInterface::class);
        }

        $this->environment = $environment;
        $this->class = $class;
    }

    public function getEnvironment(): string
    {
        return $this->environment;
    }

    public function getClass(): string
    {
        return $this->class;
    }
}
