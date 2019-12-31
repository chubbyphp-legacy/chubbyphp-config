<?php

declare(strict_types=1);

namespace Chubbyphp\Config\ServiceProvider;

use Chubbyphp\Config\ConfigInterface;
use Chubbyphp\Config\ConfigMerger;
use Chubbyphp\Config\ConfigProviderInterface;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

final class ConfigServiceProvider implements ServiceProviderInterface
{
    /**
     * @var ConfigInterface|null
     */
    private $config;

    /**
     * @var ConfigProviderInterface
     */
    private $configProvider;

    /**
     * @param ConfigInterface|ConfigProviderInterface|mixed $config
     */
    public function __construct($config)
    {
        if ($config instanceof ConfigInterface) {
            $this->config = $config;

            return;
        }

        if ($config instanceof ConfigProviderInterface) {
            $this->triggerConfigProviderDeprecation();
            $this->configProvider = $config;

            return;
        }

        $this->throwInvalidTypeError($config);
    }

    public function register(Container $container): void
    {
        $config = $this->config ?? $this->configProvider->get($container['env']);

        foreach ($config->getConfig() as $key => $value) {
            if (isset($container[$key])) {
                $value = ConfigMerger::merge($container[$key], $value, $key);
            }

            $container[$key] = $value;
        }

        $directories = $config->getDirectories();

        $container['chubbyphp.config.directories'] = $directories;
        foreach ($directories as $directory) {
            if (!is_dir($directory)) {
                mkdir($directory, 0775, true);
            }
        }
    }

    private function triggerConfigProviderDeprecation(): void
    {
        @trigger_error(
            sprintf(
                'Use "%s" instead of "%s" as __construct argument',
                ConfigInterface::class,
                ConfigProviderInterface::class
            ),
            E_USER_DEPRECATED
        );
    }

    /**
     * @param mixed $config
     */
    private function throwInvalidTypeError($config): void
    {
        throw new \TypeError(
            sprintf(
                '%s::__construct() expects parameter 1 to be %s|%s, %s given',
                self::class,
                ConfigInterface::class,
                ConfigProviderInterface::class,
                is_object($config) ? get_class($config) : gettype($config)
            )
        );
    }
}
