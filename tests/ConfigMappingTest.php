<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Config;

use Chubbyphp\Config\ConfigInterface;
use Chubbyphp\Config\ConfigMapping;
use PHPUnit\Framework\TestCase;
use Chubbyphp\Config\ConfigException;

/**
 * @covers \Chubbyphp\Config\ConfigMapping
 */
class ConfigMappingTest extends TestCase
{
    public function testCreateWithUnsupportedClass()
    {
        $this->expectException(ConfigException::class);
        $this->expectExceptionMessage('Class "stdClass" does not implement interface "Chubbyphp\Config\ConfigInterface"');

        new ConfigMapping('dev', \stdClass::class);
    }

    public function testCreateWithSupportedClass()
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
                return [];
            }

            /**
             * @return array
             */
            public function getDirectories(): array
            {
                return [];
            }
        };

        $class = get_class($object);

        $mapping = new ConfigMapping('dev', $class);

        self::assertSame('dev', $mapping->getEnvironment());
        self::assertSame($class, $mapping->getClass());
    }
}
