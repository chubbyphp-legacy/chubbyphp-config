<?php

namespace Chubbyphp\Tests\Config;

use Chubbyphp\Config\ConfigException;
use Chubbyphp\Config\ConfigProvider;
use Chubbyphp\Config\ConfigInterface;
use Chubbyphp\Config\ConfigMappingInterface;
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

        $factory = new ConfigProvider('/root', []);

        $config = $factory->create('dev');
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
            public function __construct(string $rootDir = null)
            {
                $this->rootDir = $rootDir;
            }

            /**
             * @return array
             */
            public function getSettings(): array
            {
                return ['rootDir' => $this->rootDir];
            }

            /**
             * @return array
             */
            public function getRequiredDirectories(): array
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

        $factory = new ConfigProvider('/root', [
            $configMapping,
        ]);

        $config = $factory->create('dev');

        self::assertInstanceOf($class, $config);

        self::assertSame(['rootDir' => '/root'], $config->getSettings());
        self::assertSame(['/path/to/create'], $config->getRequiredDirectories());
    }
}
