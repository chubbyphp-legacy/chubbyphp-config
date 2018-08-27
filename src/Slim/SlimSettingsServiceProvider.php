<?php

declare(strict_types=1);

namespace Chubbyphp\Config\Slim;

use Chubbyphp\Config\ConfigException;
use Chubbyphp\Config\ConfigProviderInterface;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Slim\Interfaces\CollectionInterface;

final class SlimSettingsServiceProvider implements ServiceProviderInterface
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

        if (!$config instanceof SlimSettingsInterface) {
            throw ConfigException::createByMissingInterface(SlimSettingsInterface::class);
        }

        $container->extend('settings', function (CollectionInterface $settings) use ($config) {
            $settings->replace($config->getSlimSettings());

            return $settings;
        });
    }
}
