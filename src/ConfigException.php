<?php

declare(strict_types=1);

namespace Chubbyphp\Config;

final class ConfigException extends \InvalidArgumentException
{
    public static function createByEnvironment(string $environment): self
    {
        return new self(sprintf('There is no config for environment "%s"', $environment));
    }

    public static function createByMissingInterface(string $class, string $interface): self
    {
        return new self(sprintf('Class "%s" does not implement interface "%s"', $class, $interface));
    }
}
