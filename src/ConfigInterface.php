<?php

declare(strict_types=1);

namespace Chubbyphp\Config;

interface ConfigInterface
{
    /**
     * @return array
     */
    public function getSettings(): array;

    /**
     * @return array
     */
    public function getRequiredDirectories(): array;
}
