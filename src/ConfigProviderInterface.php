<?php

declare(strict_types=1);

namespace Chubbyphp\Config;

interface ConfigProviderInterface
{
    public function get(string $environment): ConfigInterface;
}
