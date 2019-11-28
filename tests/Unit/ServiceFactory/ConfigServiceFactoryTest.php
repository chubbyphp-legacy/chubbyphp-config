<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Config\Unit\ServiceFactory;

use Chubbyphp\Config\ConfigInterface;
use Chubbyphp\Config\ServiceFactory\ConfigServiceFactory;
use Chubbyphp\Container\Container;
use Chubbyphp\Container\Exceptions\ContainerException;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Chubbyphp\Config\ServiceFactory\ConfigServiceFactory
 *
 * @internal
 */
final class ConfigServiceFactoryTest extends TestCase
{
    use MockByCallsTrait;

    public function testRegister(): void
    {
        $container = new Container();

        $directory = sys_get_temp_dir().'/config-service-provider-'.uniqid();

        $directories = ['sample' => $directory];

        /** @var ConfigInterface|MockObject $config */
        $config = $this->getMockByCalls(ConfigInterface::class, [
            Call::create('getConfig')->with()->willReturn(['key' => 'value']),
            Call::create('getDirectories')->with()->willReturn($directories),
        ]);

        self::assertDirectoryNotExists($directory);

        $container->factories((new ConfigServiceFactory($config))());

        self::assertTrue($container->has('key'));
        self::assertSame('value', $container->get('key'));

        self::assertTrue($container->has('chubbyphp.config.directories'));

        self::assertSame($directories, $container->get('chubbyphp.config.directories'));

        self::assertDirectoryExists($directory);
    }

    public function testRegisterWithExistingScalar(): void
    {
        $container = new Container([
            'key' => static function () { return 'existingValue'; },
        ]);

        $directory = sys_get_temp_dir().'/config-service-provider-'.uniqid();

        $directories = ['sample' => $directory];

        /** @var ConfigInterface|MockObject $config */
        $config = $this->getMockByCalls(ConfigInterface::class, [
            Call::create('getConfig')->with()->willReturn(['key' => 'value']),
            Call::create('getDirectories')->with()->willReturn($directories),
        ]);

        self::assertDirectoryNotExists($directory);

        $container->factories((new ConfigServiceFactory($config))());

        self::assertTrue($container->has('key'));
        self::assertSame('value', $container->get('key'));

        self::assertDirectoryExists($directory);
    }

    public function testRegisterWithExistingArray(): void
    {
        $container = new Container([
            'key' => static function () {
                return [
                    'key1' => [
                        'key11' => 'value11',
                        'key12' => 'value12',
                    ],
                    'key2' => 'value2',
                    'key3' => [
                        0 => 'value31',
                        2 => 'value32',
                    ],
                ];
            },
        ]);

        $directory = sys_get_temp_dir().'/config-service-provider-'.uniqid();

        $directories = ['sample' => $directory];

        /** @var ConfigInterface|MockObject $config */
        $config = $this->getMockByCalls(ConfigInterface::class, [
            Call::create('getConfig')->with()->willReturn([
                'env' => 'test',
                'key' => [
                    'key1' => [
                        'key12' => 'value112',
                    ],
                    'key3' => [
                        'value33',
                        'value34',
                    ],
                    'key4' => 'value4',
                ],
            ]),
            Call::create('getDirectories')->with()->willReturn($directories),
        ]);

        self::assertDirectoryNotExists($directory);

        $container->factories((new ConfigServiceFactory($config))());

        self::assertTrue($container->has('env'));

        self::assertSame('test', $container->get('env'));

        self::assertTrue($container->has('key'));
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
                4 => 'value34',
            ],
            'key4' => 'value4',
        ], $container->get('key'));

        self::assertDirectoryExists($directory);

        self::assertSame('0775', substr(sprintf('%o', fileperms($directory)), -4));
    }

    public function testRegisterWithExistingStringConvertToInt(): void
    {
        $container = new Container([
            'key' => static function () { return 'value'; },
        ]);

        $directory = sys_get_temp_dir().'/config-service-provider-'.uniqid();

        $directories = ['sample' => $directory];

        /** @var ConfigInterface|MockObject $config */
        $config = $this->getMockByCalls(ConfigInterface::class, [
            Call::create('getConfig')->with()->willReturn(['key' => 1]),
            Call::create('getDirectories')->with()->willReturn($directories),
        ]);

        self::assertDirectoryNotExists($directory);

        $container->factories((new ConfigServiceFactory($config))());

        self::assertDirectoryExists($directory);

        self::assertSame('0775', substr(sprintf('%o', fileperms($directory)), -4));

        try {
            $container->get('key');

            self::fail(sprintf('expected "%s"', ContainerException::class));
        } catch (ContainerException $exception) {
            self::assertSame('Could not create service with id "key"', $exception->getMessage());
            self::assertSame(
                'Type conversion from "string" to "integer" at path "key"',
                $exception->getPrevious()->getMessage()
            );
        }
    }

    public function testRegisterWithExistingStringConvertToArray(): void
    {
        $container = new Container([
            'key' => static function () {
                return [
                    'key1' => [
                        'key11' => 'value11',
                        'key12' => 'value12',
                    ],
                    'key2' => 'value2',
                    'key3' => [
                        0 => 'value31',
                        2 => 'value32',
                    ],
                ];
            },
        ]);

        $directory = sys_get_temp_dir().'/config-service-provider-'.uniqid();

        $directories = ['sample' => $directory];

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
            Call::create('getDirectories')->with()->willReturn($directories),
        ]);

        self::assertDirectoryNotExists($directory);

        $container->factories((new ConfigServiceFactory($config))());

        self::assertDirectoryExists($directory);

        self::assertSame('0775', substr(sprintf('%o', fileperms($directory)), -4));

        try {
            $container->get('key');

            self::fail(sprintf('expected "%s"', ContainerException::class));
        } catch (ContainerException $exception) {
            self::assertSame('Could not create service with id "key"', $exception->getMessage());
            self::assertSame(
                'Type conversion from "string" to "array" at path "key.key1.key12"',
                $exception->getPrevious()->getMessage()
            );
        }
    }
}
