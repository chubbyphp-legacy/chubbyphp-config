<?php

declare(strict_types=1);

namespace Chubbyphp\Config\ServiceProvider;

use Chubbyphp\Config\ConfigProviderInterface;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

final class ConfigServiceProvider implements ServiceProviderInterface
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

        foreach ($config->getConfig() as $key => $value) {
            if (isset($container[$key])) {
                $container[$key] = $this->mergeRecursive($container[$key], $value, $key);
            } else {
                $container[$key] = $value;
            }
        }

        $container['chubbyphp.config.directories'] = $config->getDirectories();
        foreach ($container['chubbyphp.config.directories'] as $requiredDirectory) {
            if (!is_dir($requiredDirectory)) {
                mkdir($requiredDirectory, 0777, true);
            }
        }
    }

    /**
     * @param array|string|float|int|bool $existingValue
     * @param array|string|float|int|bool $newValue
     * @param string                      $path
     *
     * @return array|string|float|int|bool
     */
    private function mergeRecursive($existingValue, $newValue, string $path)
    {
        $existingType = gettype($existingValue);
        $newType = gettype($newValue);

        if ($existingType !== $newType) {
            throw new \LogicException(
                sprintf('Type conversion from "%s" to "%s" at path "%s"', $existingType, $newType, $path)
            );
        }

        if ('array' !== $newType) {
            return $newValue;
        }

        foreach ($newValue as $key => $newSubValue) {
            if (!is_string($key)) {
                $existingValue[] = $newSubValue;

                continue;
            }

            if (isset($existingValue[$key])) {
                $subPath = $path.'.'.$key;

                $existingValue[$key] = $this->mergeRecursive($existingValue[$key], $newSubValue, $subPath);
            } else {
                $existingValue[$key] = $newSubValue;
            }
        }

        return $existingValue;
    }
}
