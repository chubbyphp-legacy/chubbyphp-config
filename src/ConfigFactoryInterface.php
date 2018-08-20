<?php

declare(strict_types=1);

namespace Chubbyphp\Config;

interface ConfigFactoryInterface
{
    /**
     * @param string $rootDir
     * @param string $environment
     *
     * @return ConfigInterface
     */
    public function create(string $rootDir, string $environment): ConfigInterface;
}
