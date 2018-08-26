<?php

declare(strict_types=1);

namespace Chubbyphp\Config;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

final class ConfigServiceProvider implements ServiceProviderInterface
{
    /**
     * @var ConfigFactoryInterface
     */
    private $configFactory;

    /**
     * @var string
     */
    private $rootDir;

    /**
     * @param ConfigFactoryInterface $configFactory
     * @param string                 $rootDir
     */
    public function __construct(ConfigFactoryInterface $configFactory, string $rootDir)
    {
        $this->configFactory = $configFactory;
        $this->rootDir = $rootDir;
    }

    /**
     * @param Container $container
     */
    public function register(Container $container)
    {
        $config = $this->configFactory->create($this->rootDir, $container['environment']);

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
