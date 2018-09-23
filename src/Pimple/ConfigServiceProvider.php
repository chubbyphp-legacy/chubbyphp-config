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
            if (isset($container[$key])) {
                $container[$key] = $this->replace($key, $container[$key], $value);
            } else {
                $container[$key] = $value;
            }
        }

        foreach ($config->getDirectories() as $requiredDirectory) {
            if (!is_dir($requiredDirectory)) {
                mkdir($requiredDirectory, 0777, true);
            }
        }
    }

    /**
     * @param string                 $key
     * @param array|string|int|float $existingValue
     * @param array|string|int|float $value
     */
    private function replace(string $key, $existingValue, $value)
    {
        $existingValueType = gettype($existingValue);
        $valueType = gettype($value);

        if ($existingValueType !== $valueType) {
            throw new \LogicException(
                sprintf(
                    'Key "%s" already exist with type "%s", new type "%s"',
                    $key,
                    $existingValueType,
                    $valueType
                )
            );
        }

        if ('array' === $valueType) {
            return array_replace_recursive($existingValue, $value);
        }

        return $value;
    }
}
