<?php

declare(strict_types=1);

namespace Chubbyphp\Config;

final class ConfigException extends \InvalidArgumentException
{
    public static function createByEnvironment(string $environment): self
    {
        return new self(sprintf('There is no config for environment "%s"', $environment));
    }
}
