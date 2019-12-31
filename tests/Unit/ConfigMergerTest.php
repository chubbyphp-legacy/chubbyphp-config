<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Config\Unit\ServiceFactory;

use Chubbyphp\Config\ConfigMerger;
use Chubbyphp\Mock\MockByCallsTrait;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Chubbyphp\Config\ConfigMerger
 *
 * @internal
 */
final class ConfigMergerTest extends TestCase
{
    use MockByCallsTrait;

    /**
     * @dataProvider provideValidReplacements
     *
     * @param array|string|float|int|bool $expectedValue
     * @param array|string|float|int|bool $existingValue
     * @param array|string|float|int|bool $newValue
     */
    public function testWithValidScalarValues($expectedValue, $existingValue, $newValue): void
    {
        self::assertSame($expectedValue, ConfigMerger::merge($existingValue, $newValue, 'key'));
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public function provideValidReplacements(): array
    {
        return [
            'nullToString' => [
                'expectedValue' => 'newValue',
                'existingValue' => null,
                'newValue' => 'newValue',
            ],
            'stringToNull' => [
                'expectedValue' => null,
                'existingValue' => 'existingValue',
                'newValue' => null,
            ],
            'stringToString' => [
                'expectedValue' => 'newValue',
                'existingValue' => 'existingValue',
                'newValue' => 'newValue',
            ],
            'nullToFloat' => [
                'expectedValue' => 1.2,
                'existingValue' => null,
                'newValue' => 1.2,
            ],
            'floatToNull' => [
                'expectedValue' => null,
                'existingValue' => 1.1,
                'newValue' => null,
            ],
            'floatToFloat' => [
                'expectedValue' => 1.2,
                'existingValue' => 1.1,
                'newValue' => 1.2,
            ],
            'nullToInt' => [
                'expectedValue' => 2,
                'existingValue' => null,
                'newValue' => 2,
            ],
            'intToNull' => [
                'expectedValue' => null,
                'existingValue' => 1,
                'newValue' => null,
            ],
            'intToInt' => [
                'expectedValue' => 2,
                'existingValue' => 1,
                'newValue' => 2,
            ],
            'intToFloat' => [
                'expectedValue' => 1.1,
                'existingValue' => 1,
                'newValue' => 1.1,
            ],
            'nullToBool' => [
                'expectedValue' => true,
                'existingValue' => null,
                'newValue' => true,
            ],
            'boolToNull' => [
                'expectedValue' => null,
                'existingValue' => false,
                'newValue' => null,
            ],
            'boolToBool' => [
                'expectedValue' => true,
                'existingValue' => false,
                'newValue' => true,
            ],
        ];
    }

    /**
     * @dataProvider provideInValidReplacements
     *
     * @param array|string|float|int|bool $existingValue
     * @param array|string|float|int|bool $newValue
     */
    public function testWithInValidScalarValues(string $expectedExceptionMessage, $existingValue, $newValue): void
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage($expectedExceptionMessage);

        ConfigMerger::merge($existingValue, $newValue, 'key');
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public function provideInValidReplacements(): array
    {
        return [
            'stringToFloat' => [
                'expectedExceptionMessage' => 'Type conversion from "string" to "double" at path "key"',
                'existingValue' => 'existingValue',
                'newValue' => 1.1,
            ],
            'stringToInt' => [
                'expectedExceptionMessage' => 'Type conversion from "string" to "integer" at path "key"',
                'existingValue' => 'existingValue',
                'newValue' => 1,
            ],
            'stringToBool' => [
                'expectedExceptionMessage' => 'Type conversion from "string" to "boolean" at path "key"',
                'existingValue' => 'existingValue',
                'newValue' => true,
            ],
            'floatToString' => [
                'expectedExceptionMessage' => 'Type conversion from "double" to "string" at path "key"',
                'existingValue' => 1.1,
                'newValue' => 'newValue',
            ],
            'floatToInt' => [
                'expectedExceptionMessage' => 'Type conversion from "double" to "integer" at path "key"',
                'existingValue' => 1.1,
                'newValue' => 1,
            ],
            'floatToBool' => [
                'expectedExceptionMessage' => 'Type conversion from "double" to "boolean" at path "key"',
                'existingValue' => 1.1,
                'newValue' => true,
            ],
            'intToString' => [
                'expectedExceptionMessage' => 'Type conversion from "integer" to "string" at path "key"',
                'existingValue' => 1,
                'newValue' => 'newValue',
            ],
            'intToBool' => [
                'expectedExceptionMessage' => 'Type conversion from "integer" to "boolean" at path "key"',
                'existingValue' => 1,
                'newValue' => true,
            ],
            'boolToString' => [
                'expectedExceptionMessage' => 'Type conversion from "boolean" to "string" at path "key"',
                'existingValue' => true,
                'newValue' => 'newValue',
            ],
            'boolToFloat' => [
                'expectedExceptionMessage' => 'Type conversion from "boolean" to "double" at path "key"',
                'existingValue' => true,
                'newValue' => 1.1,
            ],
            'boolToInt' => [
                'expectedExceptionMessage' => 'Type conversion from "boolean" to "integer" at path "key"',
                'existingValue' => true,
                'newValue' => 1,
            ],
        ];
    }

    public function testWithResource(): void
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Not supported data type: resource');

        ConfigMerger::merge('', fopen('php://memory', 'r'), 'key');
    }

    public function testWithObject(): void
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Not supported data type: stdClass');

        ConfigMerger::merge('', new \stdClass(), 'key');
    }

    public function testWithValidArray(): void
    {
        self::assertSame(
            [
                'key1' => [
                    'key11' => 'value11',
                    'key12' => 'value112',
                ],
                'key2' => 'value2',
                'key3' => [
                    0 => 'value31',
                    2 => 'value32',
                    3 => 'value33',
                    4 => 'value34',
                ],
                'key4' => 'value4',
            ],
            ConfigMerger::merge(
                [
                    'key1' => [
                        'key11' => 'value11',
                        'key12' => 'value12',
                    ],
                    'key2' => 'value2',
                    'key3' => [
                        0 => 'value31',
                        2 => 'value32',
                    ],
                ],
                [
                    'key1' => [
                        'key12' => 'value112',
                    ],
                    'key3' => [
                        'value33',
                        'value34',
                    ],
                    'key4' => 'value4',
                ],
                'key'
            )
        );
    }

    public function testWithInValidArray(): void
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Type conversion from "string" to "boolean" at path "key.key2"');

        ConfigMerger::merge(
            [
                'key1' => [
                    'key11' => 'value11',
                    'key12' => 'value12',
                ],
                'key2' => 'value2',
                'key3' => [
                    0 => 'value31',
                    2 => 'value32',
                ],
            ],
            [
                'key1' => [
                    'key12' => 'value112',
                ],
                'key2' => true,
                'key3' => [
                    'value33',
                    'value34',
                ],
                'key4' => 'value4',
            ],
            'key'
        );
    }
}
