<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Config;

use Chubbyphp\Config\ConfigException;
use Chubbyphp\Config\ConfigInterface;
use Chubbyphp\Config\ConfigMappingInterface;
use Chubbyphp\Config\ConfigProvider;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Chubbyphp\Config\ConfigProvider
 */
class ConfigProviderTest extends TestCase
{
    use MockByCallsTrait;

    public function testCreateByMissingEnvironment()
    {
        $this->expectException(ConfigException::class);
        $this->expectExceptionMessage('There is no config for environment "dev"');

        $provider = new ConfigProvider('/root', []);

        $config = $provider->get('dev');
    }

    public function testCreateByEnvironment()
    {
        $object = new class() implements ConfigInterface {
            /**
             * @var string|null
             */
            private $rootDir;

            /**
             * @param string $rootDir
             */
            public static function create(string $rootDir): ConfigInterface
            {
                $config = new self();
                $config->rootDir = $rootDir;

                return $config;
            }

            /**
             * @return array
             */
            public function getConfig(): array
            {
                return ['rootDir' => $this->rootDir];
            }

            /**
             * @return array
             */
            public function getDirectories(): array
            {
                return ['/path/to/create'];
            }
        };

        $class = get_class($object);

        /** @var ConfigMappingInterface|MockObject $configMapping */
        $configMapping = $this->getMockByCalls(ConfigMappingInterface::class, [
            Call::create('getEnvironment')->with()->willReturn('dev'),
            Call::create('getClass')->with()->willReturn($class),
        ]);

        $provider = new ConfigProvider('/root', [
            $configMapping,
        ]);

        $config = $provider->get('dev');

        self::assertInstanceOf($class, $config);

        self::assertSame(['rootDir' => '/root'], $config->getConfig());
        self::assertSame(['/path/to/create'], $config->getDirectories());
    }
}
