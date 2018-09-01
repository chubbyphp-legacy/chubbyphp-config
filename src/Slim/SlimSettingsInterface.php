<?php

declare(strict_types=1);

namespace Chubbyphp\Config\Slim;

interface SlimSettingsInterface
{
    /**
     * @return array
     */
    public function getSlimSettings(): array;
}
