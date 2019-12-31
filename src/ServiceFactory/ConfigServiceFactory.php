<?php

declare(strict_types=1);

namespace Chubbyphp\Config\ServiceFactory;

use Chubbyphp\Config\ConfigInterface;
use Chubbyphp\Config\ConfigMerger;
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

    /**
     * @return array<string, callable>
     */
    public function __invoke(): array
    {
        $factories = [];
        foreach ($this->config->getConfig() as $key => $value) {
            $factories[$key] = static function (
                ContainerInterface $container,
                ?callable $previous = null
            ) use ($key, $value) {
                if (null !== $previous) {
                    $value = ConfigMerger::merge($previous($container), $value, $key);
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
}
