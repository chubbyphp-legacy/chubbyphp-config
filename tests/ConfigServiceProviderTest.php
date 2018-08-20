<?php

namespace Chubbyphp\Tests\Config;

use Chubbyphp\Config\ConfigFactoryInterface;
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

        /** @var ConfigInterface|MockObject $config */
        $config = $this->getMockByCalls(ConfigInterface::class, [
            Call::create('getSettings')->with()->willReturn(['key' => 'value']),
        ]);

        /** @var ConfigFactoryInterface|MockObject $configMapping */
        $factory = $this->getMockByCalls(ConfigFactoryInterface::class, [
            Call::create('create')->with('/root', 'dev')->willReturn($config),
        ]);

        $serviceProvider = new ConfigServiceProvider($factory, '/root');
        $serviceProvider->register($container);
    }
}
