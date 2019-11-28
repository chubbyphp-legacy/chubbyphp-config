<?php

declare(strict_types=1);

namespace Chubbyphp\Config\ServiceFactory;

use Chubbyphp\Config\ConfigInterface;
use Chubbyphp\Container\ContainerInterface;

final class ConfigServiceFactory
{
    /**
     * @var ConfigInterface
     */
    private $config;

    public function __construct(ConfigInterface $config)
    {
        $this->config = $config;
    }

    public function __invoke(): array
    {
        $factories = [];
        foreach ($this->config->getConfig() as $key => $value) {
            $factories[$key] = function (ContainerInterface $container, ?callable $previous = null) use ($key, $value) {
                if (null !== $previous) {
                    $value = $this->mergeRecursive($previous($container), $value, $key);
                }

                return $value;
            };
        }

        $directories = $this->config->getDirectories();

        $factories['chubbyphp.config.directories'] = static function () use ($directories) {
            return $directories;
        };

        foreach ($directories as $directory) {
            if (!is_dir($directory)) {
                mkdir($directory, 0775, true);
            }
        }

        return $factories;
    }

    /**
     * @param array|string|float|int|bool $existingValue
     * @param array|string|float|int|bool $newValue
     *
     * @return array|string|float|int|bool
     */
    private function mergeRecursive($existingValue, $newValue, string $path)
    {
        $this->assertSameType($existingValue, $newValue, $path);

        if ('array' !== gettype($newValue)) {
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

    /**
     * @param array|string|float|int|bool $existingValue
     * @param array|string|float|int|bool $newValue
     */
    private function assertSameType($existingValue, $newValue, string $path): void
    {
        $existingType = gettype($existingValue);
        $newType = gettype($newValue);

        if ($existingType !== $newType) {
            throw new \LogicException(
                sprintf('Type conversion from "%s" to "%s" at path "%s"', $existingType, $newType, $path)
            );
        }
    }
}
