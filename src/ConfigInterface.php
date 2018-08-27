<?php

declare(strict_types=1);

namespace Chubbyphp\Config;

interface ConfigInterface
{
    /**
     * @param string $rootDir
     *
     * @return self
     */
    public static function create(string $rootDir): self;

    /**
     * @return array
     */
    public function getConfig(): array;

    /**
     * @return array
     */
    public function getDirectories(): array;
}
