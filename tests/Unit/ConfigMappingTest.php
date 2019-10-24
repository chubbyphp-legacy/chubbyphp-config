<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Config\Unit;

use Chubbyphp\Config\ConfigException;
use Chubbyphp\Config\ConfigInterface;
use Chubbyphp\Config\ConfigMapping;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Chubbyphp\Config\ConfigMapping
 *
 * @internal
 */
class ConfigMappingTest extends TestCase
{
    public function testCreateWithUnsupportedClass(): void
    {
        $this->expectException(ConfigException::class);
        $this->expectExceptionMessage('Class "stdClass" does not implement interface "Chubbyphp\Config\ConfigInterface"');

        new ConfigMapping('dev', \stdClass::class);
    }

    public function testCreateWithSupportedClass(): void
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
