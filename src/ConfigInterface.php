<?php

declare(strict_types=1);

namespace Chubbyphp\Config;

interface ConfigInterface
{
    public function getConfig(): array;

    /**
     * @return array<string, string>
     */
    public function getDirectories(): array;

    public function getEnv(): string;
}
