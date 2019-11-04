<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Config\Unit;

use Chubbyphp\Config\ConfigException;
use Chubbyphp\Config\ConfigInterface;
use Chubbyphp\Config\ConfigProviderInterface;
use Chubbyphp\Config\Slim\SlimSettingsInterface;
use Chubbyphp\Config\Slim\SlimSettingsServiceProvider;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Pimple\Container;
use Slim\Collection;

/**
 * @covers \Chubbyphp\Config\Slim\SlimSettingsServiceProvider
 *
 * @internal
 */
final class SlimSettingsServiceProviderTest extends TestCase
{
    use MockByCallsTrait;

    public function testRegisterWithConfigInterface(): void
    {
        /** @var ConfigInterface|MockObject $config */
        $config = $this->getMockByCalls(ConfigInterface::class);

        $this->expectException(ConfigException::class);
        $this->expectExceptionMessage(
            sprintf(
                'Class "%s" does not implement interface "Chubbyphp\Config\Slim\SlimSettingsInterface"',
                get_class($config)
            )
        );

        $container = new Container([
            'env' => 'dev',
            'settings' => function () {
                return new Collection(['displayErrorDetails' => false]);
            },
        ]);

        /** @var ConfigProviderInterface|MockObject $provider */
        $provider = $this->getMockByCalls(ConfigProviderInterface::class, [
            Call::create('get')->with('dev')->willReturn($config),
        ]);

        $serviceProvider = new SlimSettingsServiceProvider($provider);
        $serviceProvider->register($container);
    }

    public function testRegisterWithSlimSettingsInterface(): void
    {
        $container = new Container([
            'env' => 'dev',
            'settings' => function () {
                return new Collection(['displayErrorDetails' => false]);
            },
        ]);

        /** @var SlimSettingsInterface|MockObject $config */
        $config = $this->getMockByCalls([ConfigInterface::class, SlimSettingsInterface::class], [
            Call::create('getSlimSettings')->with()->willReturn(['displayErrorDetails' => true]),
        ]);

        /** @var ConfigProviderInterface|MockObject $provider */
        $provider = $this->getMockByCalls(ConfigProviderInterface::class, [
            Call::create('get')->with('dev')->willReturn($config),
        ]);

        $container->register(new SlimSettingsServiceProvider($provider));

        self::assertSame(['displayErrorDetails' => true], $container['settings']->all());
    }
}
