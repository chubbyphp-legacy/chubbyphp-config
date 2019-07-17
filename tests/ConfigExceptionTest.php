<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Config;

use Chubbyphp\Config\ConfigException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Chubbyphp\Config\ConfigException
 *
 * @internal
 */
class ConfigExceptionTest extends TestCase
{
    public function testCreateByEnvironment()
    {
        $exception = ConfigException::createByEnvironment('dev');

        self::assertSame('There is no config for environment "dev"', $exception->getMessage());
    }

    public function testCreateByMissingInterface()
    {
        $exception = ConfigException::createByMissingInterface(\stdClass::class, \DateTimeInterface::class);

        self::assertSame('Class "stdClass" does not implement interface "DateTimeInterface"', $exception->getMessage());
    }
}
