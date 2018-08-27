<?php

declare(strict_types=1);

namespace Chubbyphp\Config\Pimple;

use Chubbyphp\Config\ConfigProviderInterface;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

final class ConfigServiceProvider implements ServiceProviderInterface
{
    /**
     * @var ConfigProviderInterface
     */
    private $configProvider;

    /**
     * @param ConfigProviderInterface $configProvider
     */
    public function __construct(ConfigProviderInterface $configProvider)
    {
        $this->configProvider = $configProvider;
    }

    /**
     * @param Container $container
     */
    public function register(Container $container)
    {
        $config = $this->configProvider->get($container['env']);

        foreach ($config->getConfig() as $key => $value) {
            $container[$key] = $value;
        }

        foreach ($config->getDirectories() as $requiredDirectory) {
            if (!is_dir($requiredDirectory)) {
                mkdir($requiredDirectory, 0777);
            }
        }
    }
}
