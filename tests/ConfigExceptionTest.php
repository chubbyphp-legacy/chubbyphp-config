<?php

namespace Chubbyphp\Tests\Config;

use PHPUnit\Framework\TestCase;
use Chubbyphp\Config\ConfigException;

/**
 * @covers \Chubbyphp\Config\ConfigException
 */
class ConfigExceptionTest extends TestCase
{
    public function testCreateByEnvironment()
    {
        $exception = ConfigException::createByEnvironment('dev');

        self::assertSame('There is no config for environment "dev"', $exception->getMessage());
    }
}
