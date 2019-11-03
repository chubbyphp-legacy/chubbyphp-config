<?php

declare(strict_types=1);

namespace Chubbyphp\Config;

interface ConfigInterface
{
    public static function create(string $rootDir): self;

    public function getConfig(): array;

    /**
     * @return array<string, string>
     */
    public function getDirectories(): array;
}
