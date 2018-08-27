<?php

declare(strict_types=1);

namespace Chubbyphp\Config;

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
        $config = $this->configProvider->create($container['environment']);

        foreach ($config->getSettings() as $key => $value) {
            $container[$key] = $value;
        }

        foreach ($config->getRequiredDirectories() as $requiredDirectory) {
            if (!is_dir($requiredDirectory)) {
                mkdir($requiredDirectory, 0777);
            }
        }
    }
}
