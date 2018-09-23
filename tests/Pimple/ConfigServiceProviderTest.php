<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Config\Pimple;

use Chubbyphp\Config\ConfigInterface;
use Chubbyphp\Config\ConfigProviderInterface;
use Chubbyphp\Config\Pimple\ConfigServiceProvider;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Pimple\Container;

/**
 * @covers \Chubbyphp\Config\Pimple\ConfigServiceProvider
 */
class ConfigServiceProviderTest extends TestCase
{
    use MockByCallsTrait;

    public function testRegister()
    {
        $container = new Container(['env' => 'dev']);

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

        $container->register(new ConfigServiceProvider($provider));

        self::assertArrayHasKey('key', $container);
        self::assertSame('value', $container['key']);

        self::assertDirectoryExists($directory);
    }

    public function testRegisterWithExistingScalar()
    {
        $container = new Container(['env' => 'dev', 'key' => 'existingValue']);

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

        $container->register(new ConfigServiceProvider($provider));

        self::assertArrayHasKey('key', $container);
        self::assertSame('value', $container['key']);

        self::assertDirectoryExists($directory);
    }

    public function testRegisterWithExistingArray()
    {
        $container = new Container([
            'env' => 'dev',
            'key' => [
                'key1' => 'value1',
                'key2' => 'value2',
            ],
        ]);

        $directory = sys_get_temp_dir().'/config-service-provider-'.uniqid();

        /** @var ConfigInterface|MockObject $config */
        $config = $this->getMockByCalls(ConfigInterface::class, [
            Call::create('getConfig')->with()->willReturn(['key' => ['key2' => 'value22', 'key3' => 'value3']]),
            Call::create('getDirectories')->with()->willReturn([$directory]),
        ]);

        /** @var ConfigProviderInterface|MockObject $provider */
        $provider = $this->getMockByCalls(ConfigProviderInterface::class, [
            Call::create('get')->with('dev')->willReturn($config),
        ]);

        self::assertDirectoryNotExists($directory);

        $container->register(new ConfigServiceProvider($provider));

        self::assertArrayHasKey('key', $container);
        self::assertSame(['key1' => 'value1', 'key2' => 'value22', 'key3' => 'value3'], $container['key']);

        self::assertDirectoryExists($directory);
    }

    public function testRegisterWithExistingStringCovertToArray()
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Key "key" already exist with type "string", new type "array"');

        $container = new Container([
            'env' => 'dev',
            'key' => 'value',
        ]);

        $directory = sys_get_temp_dir().'/config-service-provider-'.uniqid();

        /** @var ConfigInterface|MockObject $config */
        $config = $this->getMockByCalls(ConfigInterface::class, [
            Call::create('getConfig')->with()->willReturn(['key' => ['key1' => 'value1']]),
        ]);

        /** @var ConfigProviderInterface|MockObject $provider */
        $provider = $this->getMockByCalls(ConfigProviderInterface::class, [
            Call::create('get')->with('dev')->willReturn($config),
        ]);

        self::assertDirectoryNotExists($directory);

        $container->register(new ConfigServiceProvider($provider));
    }
}
