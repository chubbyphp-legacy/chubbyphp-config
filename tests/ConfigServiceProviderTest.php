<?php

namespace Chubbyphp\Tests\Config;

use Chubbyphp\Config\ConfigProviderInterface;
use Chubbyphp\Config\ConfigInterface;
use Chubbyphp\Config\ConfigServiceProvider;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Pimple\Container;

/**
 * @covers \Chubbyphp\Config\ConfigServiceProvider
 */
class ConfigServiceProviderTest extends TestCase
{
    use MockByCallsTrait;

    public function testRegister()
    {
        $container = new Container(['environment' => 'dev']);

        $directory = sys_get_temp_dir().'/config-service-provider-'.uniqid();

        /** @var ConfigInterface|MockObject $config */
        $config = $this->getMockByCalls(ConfigInterface::class, [
            Call::create('getConfig')->with()->willReturn(['key' => 'value']),
            Call::create('getDirectories')->with()->willReturn([$directory]),
        ]);

        /** @var ConfigProviderInterface|MockObject $provider */
        $provider = $this->getMockByCalls(ConfigProviderInterface::class, [
            Call::create('get')->with('dev')->willReturn($config),
        ]);

        self::assertDirectoryNotExists($directory);

        $serviceProvider = new ConfigServiceProvider($provider);
        $serviceProvider->register($container);

        self::assertDirectoryExists($directory);
    }
}
