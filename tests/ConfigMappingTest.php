<?php

namespace Chubbyphp\Tests\Config;

use PHPUnit\Framework\TestCase;
use Chubbyphp\Config\ConfigMapping;

/**
 * @covers \Chubbyphp\Config\ConfigMapping
 */
class ConfigMappingTest extends TestCase
{
    public function testCreateByEnvironment()
    {
        $mapping = new ConfigMapping('dev', \stdClass::class);

        self::assertSame('dev', $mapping->getEnvironment());
        self::assertSame(\stdClass::class, $mapping->getClass());
    }
}
