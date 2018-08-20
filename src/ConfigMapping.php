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

    /**
     * @param string $environment
     * @param string $class
     */
    public function __construct(string $environment, string $class)
    {
        $this->environment = $environment;
        $this->class = $class;
    }

    /**
     * @return string
     */
    public function getEnvironment(): string
    {
        return $this->environment;
    }

    /**
     * @return string
     */
    public function getClass(): string
    {
        return $this->class;
    }
}
