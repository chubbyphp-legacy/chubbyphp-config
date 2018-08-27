<?php

declare(strict_types=1);

namespace Chubbyphp\Config\Slim;

use Chubbyphp\Config\ConfigInterface;

interface SlimSettingsInterface extends ConfigInterface
{
    /**
     * @return array
     */
    public function getSlimSettings(): array;
}
