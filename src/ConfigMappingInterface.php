<?php

declare(strict_types=1);

namespace Chubbyphp\Config;

interface ConfigMappingInterface
{
    public function getEnvironment(): string;

    public function getClass(): string;
}
