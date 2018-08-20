<?php

namespace Chubbyphp\Tests\Config;

use Chubbyphp\Config\ConfigException;
use Chubbyphp\Config\ConfigFactory;
use Chubbyphp\Config\ConfigInterface;
use Chubbyphp\Config\ConfigMappingInterface;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Chubbyphp\Config\ConfigFactory
 */
class ConfigFactoryTest extends TestCase
{
    use MockByCallsTrait;

    public function testCreateByMissingEnvironment()
    {
        $this->expectException(ConfigException::class);
        $this->expectExceptionMessage('There is no config for environment "dev"');

        $factory = new ConfigFactory([]);

        $config = $factory->create('/root', 'dev');
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
        };

        $class = get_class($object);

        /** @var ConfigMappingInterface|MockObject $configMapping */
        $configMapping = $this->getMockByCalls(ConfigMappingInterface::class, [
            Call::create('getEnvironment')->with()->willReturn('dev'),
            Call::create('getClass')->with()->willReturn($class),
        ]);

        $factory = new ConfigFactory([
            $configMapping,
        ]);

        $config = $factory->create('/root', 'dev');

        self::assertInstanceOf($class, $config);

        self::assertSame(['rootDir' => '/root'], $config->getSettings());
    }
}
