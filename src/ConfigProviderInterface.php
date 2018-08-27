<?php

declare(strict_types=1);

namespace Chubbyphp\Config;

interface ConfigProviderInterface
{
    /**
     * @param string $environment
     *
     * @return ConfigInterface
     */
    public function get(string $environment): ConfigInterface;
}
