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

        $directories = ['sample' => $directory];

        /** @var ConfigInterface|MockObject $config */
        $config = $this->getMockByCalls(ConfigInterface::class, [
            Call::create('getConfig')->with()->willReturn(['key' => 'value']),
            Call::create('getDirectories')->with()->willReturn($directories),
        ]);

        /** @var ConfigProviderInterface|MockObject $provider */
        $provider = $this->getMockByCalls(ConfigProviderInterface::class, [
            Call::create('get')->with('dev')->willReturn($config),
        ]);

        self::assertDirectoryNotExists($directory);

        $container->register(new ConfigServiceProvider($provider));

        self::assertArrayHasKey('key', $container);
        self::assertSame('value', $container['key']);

        self::assertArrayHasKey('chubbyphp.config.directories', $container);

        self::assertSame($directories, $container['chubbyphp.config.directories']);

        self::assertDirectoryExists($directory);
    }

    public function testRegisterWithExistingScalar()
    {
        $container = new Container(['env' => 'dev', 'key' => 'existingValue']);

        $directory = sys_get_temp_dir().'/config-service-provider-'.uniqid();

        $directories = ['sample' => $directory];

        /** @var ConfigInterface|MockObject $config */
        $config = $this->getMockByCalls(ConfigInterface::class, [
            Call::create('getConfig')->with()->willReturn(['key' => 'value']),
            Call::create('getDirectories')->with()->willReturn($directories),
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
                'key1' => [
                    'key11' => 'value11',
                    'key12' => 'value12',
                ],
                'key2' => 'value2',
                'key3' => [
                    0 => 'value31',
                    2 => 'value32',
                ],
            ],
        ]);

        $directory = sys_get_temp_dir().'/config-service-provider-'.uniqid();

        $directories = ['sample' => $directory];

        /** @var ConfigInterface|MockObject $config */
        $config = $this->getMockByCalls(ConfigInterface::class, [
            Call::create('getConfig')->with()->willReturn([
                'key' => [
                    'key1' => [
                        'key12' => 'value112',
                    ],
                    'key3' => [
                        'value33',
                    ],
                ],
            ]),
            Call::create('getDirectories')->with()->willReturn($directories),
        ]);

        /** @var ConfigProviderInterface|MockObject $provider */
        $provider = $this->getMockByCalls(ConfigProviderInterface::class, [
            Call::create('get')->with('dev')->willReturn($config),
        ]);

        self::assertDirectoryNotExists($directory);

        $container->register(new ConfigServiceProvider($provider));

        self::assertArrayHasKey('key', $container);
        self::assertSame([
            'key1' => [
                'key11' => 'value11',
                'key12' => 'value112',
            ],
            'key2' => 'value2',
            'key3' => [
                0 => 'value31',
                2 => 'value32',
                3 => 'value33',
            ],
        ], $container['key']);

        self::assertDirectoryExists($directory);
    }

    public function testRegisterWithExistingStringConvertToInt()
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Type conversion from "string" to "integer" at path "key"');

        $container = new Container([
            'env' => 'dev',
            'key' => 'value',
        ]);

        $directory = sys_get_temp_dir().'/config-service-provider-'.uniqid();

        /** @var ConfigInterface|MockObject $config */
        $config = $this->getMockByCalls(ConfigInterface::class, [
            Call::create('getConfig')->with()->willReturn(['key' => 1]),
        ]);

        /** @var ConfigProviderInterface|MockObject $provider */
        $provider = $this->getMockByCalls(ConfigProviderInterface::class, [
            Call::create('get')->with('dev')->willReturn($config),
        ]);

        self::assertDirectoryNotExists($directory);

        $container->register(new ConfigServiceProvider($provider));
    }

    public function testRegisterWithExistingStringConvertToArray()
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Type conversion from "string" to "array" at path "key.key1.key12"');

        $container = new Container([
            'env' => 'dev',
            'key' => [
                'key1' => [
                    'key11' => 'value11',
                    'key12' => 'value12',
                ],
                'key2' => 'value2',
                'key3' => [
                    0 => 'value31',
                    2 => 'value32',
                ],
            ],
        ]);

        $directory = sys_get_temp_dir().'/config-service-provider-'.uniqid();

        /** @var ConfigInterface|MockObject $config */
        $config = $this->getMockByCalls(ConfigInterface::class, [
            Call::create('getConfig')->with()->willReturn([
                'key' => [
                    'key1' => [
                        'key12' => ['value112'],
                    ],
                    'key3' => [
                        'value33',
                    ],
                ],
            ]),
        ]);

        /** @var ConfigProviderInterface|MockObject $provider */
        $provider = $this->getMockByCalls(ConfigProviderInterface::class, [
            Call::create('get')->with('dev')->willReturn($config),
        ]);

        self::assertDirectoryNotExists($directory);

        $container->register(new ConfigServiceProvider($provider));
    }
}
