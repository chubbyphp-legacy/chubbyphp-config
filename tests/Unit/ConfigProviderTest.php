<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Config\Unit;

use Chubbyphp\Config\ConfigException;
use Chubbyphp\Config\ConfigInterface;
use Chubbyphp\Config\ConfigProvider;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Chubbyphp\Config\ConfigProvider
 *
 * @internal
 */
final class ConfigProviderTest extends TestCase
{
    use MockByCallsTrait;

    public function testCreateByMissingEnvironment(): void
    {
        $this->expectException(ConfigException::class);
        $this->expectExceptionMessage('There is no config for environment "dev"');

        $provider = new ConfigProvider([]);
        $provider->get('dev');
    }

    public function testCreateByEnvironment(): void
    {
        /** @var ConfigInterface|MockObject $config */
        $config = $this->getMockByCalls(ConfigInterface::class, [
            Call::create('getEnv')->with()->willReturn('dev'),
        ]);

        $provider = new ConfigProvider([$config]);

        self::assertSame($config, $provider->get('dev'));
    }
}
