<?php

declare(strict_types=1);

namespace Chubbyphp\Config;

interface ConfigMappingInterface
{
    /**
     * @return string
     */
    public function getEnvironment(): string;

    /**
     * @return string
     */
    public function getClass(): string;
}
