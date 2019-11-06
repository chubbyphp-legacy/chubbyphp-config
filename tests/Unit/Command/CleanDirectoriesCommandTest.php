<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Config\Unit\Command;

use Chubbyphp\Config\Command\CleanDirectoriesCommand;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * @covers \Chubbyphp\Config\Command\CleanDirectoriesCommand
 *
 * @internal
 */
final class CleanDirectoriesCommandTest extends TestCase
{
    public function testGetName(): void
    {
        $command = new CleanDirectoriesCommand([]);

        self::assertSame('config:clean-directories', $command->getName());
    }

    public function testWithUnsupportedDirectoryNames(): void
    {
        $path = sys_get_temp_dir().'/'.uniqid('chubbyphp-config-clean-directories-');
        $cacheDir = $path.'/cache';

        $input = new ArrayInput([
            'directoryNames' => ['cache', 'log'],
        ]);

        $output = new BufferedOutput();

        $command = new CleanDirectoriesCommand(['cache' => $cacheDir]);

        self::assertSame(1, $command->run($input, $output));

        $outputMessage = <<<'EOT'
Unsupported directory names: "log"

EOT;

        self::assertSame($outputMessage, $output->fetch());
    }

    public function testWithSupportedDirectoryNamesButMissingDirectory(): void
    {
        $path = sys_get_temp_dir().'/'.uniqid('chubbyphp-config-clean-directories-');
        $cacheDir = $path.'/cache';
        $logDir = $path.'/log';

        $input = new ArrayInput([
            'directoryNames' => ['cache', 'log'],
        ]);

        $output = new BufferedOutput();

        $command = new CleanDirectoriesCommand(['cache' => $cacheDir, 'log' => $logDir]);

        self::assertSame(2, $command->run($input, $output));

        $outputMessage = <<<'EOT'
Start clean directory with name "cache" at path "%s"
Directory with name "cache" at path "%s" could not be cleaned

EOT;

        self::assertSame(sprintf($outputMessage, $cacheDir, $cacheDir), $output->fetch());
    }

    public function testWithSupportedDirectoryNames(): void
    {
        $path = sys_get_temp_dir().'/'.uniqid('chubbyphp-config-clean-directories-');
        $cacheDir = $path.'/cache';
        $logDir = $path.'/log';

        $input = new ArrayInput([
            'directoryNames' => ['cache', 'log'],
        ]);

        $output = new BufferedOutput();

        mkdir($cacheDir.'/some/value/to/clean', 0777, true);
        touch($cacheDir.'/some/value/to/clean/file');
        mkdir($logDir.'/another/value/to/clean', 0777, true);
        touch($logDir.'/another/value/to/clean/file');

        $command = new CleanDirectoriesCommand(['cache' => $cacheDir, 'log' => $logDir]);

        $code = $command->run($input, $output);

        self::assertDirectoryNotExists($cacheDir.'/some');
        self::assertDirectoryNotExists($logDir.'/another');

        rmdir($cacheDir);
        rmdir($logDir);

        self::assertSame(0, $code);

        $outputMessage = <<<'EOT'
Start clean directory with name "cache" at path "%s"
Start clean directory with name "log" at path "%s"

EOT;

        self::assertSame(sprintf($outputMessage, $cacheDir, $logDir), $output->fetch());
    }
}
