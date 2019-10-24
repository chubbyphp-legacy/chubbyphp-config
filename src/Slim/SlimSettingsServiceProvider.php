<?php

declare(strict_types=1);

namespace Chubbyphp\Config\Slim;

use Chubbyphp\Config\ConfigException;
use Chubbyphp\Config\ConfigProviderInterface;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Slim\Interfaces\CollectionInterface;

/**
 * @deprecated
 */
final class SlimSettingsServiceProvider implements ServiceProviderInterface
{
    /**
     * @var ConfigProviderInterface
     */
    private $configProvider;

    public function __construct(ConfigProviderInterface $configProvider)
    {
        $this->configProvider = $configProvider;
    }

    public function register(Container $container): void
    {
        $config = $this->configProvider->get($container['env']);

        if (!$config instanceof SlimSettingsInterface) {
            throw ConfigException::createByMissingInterface(get_class($config), SlimSettingsInterface::class);
        }

        $container->extend('settings', static function (CollectionInterface $settings) use ($config) {
            $settings->replace($config->getSlimSettings());

            return $settings;
        });
    }
}
