<?php

declare(strict_types=1);

namespace Chubbyphp\Config;

final class ConfigException extends \InvalidArgumentException
{
    /**
     * @param string $environment
     *
     * @return self
     */
    public static function createByEnvironment(string $environment): self
    {
        return new self(sprintf('There is no config for environment "%s"', $environment));
    }

    /**
     * @param string $interface
     *
     * @return self
     */
    public static function createByMissingInterface(string $interface): self
    {
        return new self(sprintf('Missing interface "%s"', $interface));
    }
}
