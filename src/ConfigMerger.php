<?php

declare(strict_types=1);

namespace Chubbyphp\Config;

final class ConfigMerger
{
    /**
     * @param mixed $existingValue
     * @param mixed $newValue
     *
     * @return mixed
     */
    public static function merge($existingValue, $newValue, string $path)
    {
        if (null === $existingValue || null === $newValue) {
            return $newValue;
        }

        try {
            if (is_array($newValue)) {
                return self::mergeArray($existingValue, $newValue, $path);
            }
            if (is_string($newValue)) {
                return self::mergeString($existingValue, $newValue);
            }
            if (is_float($newValue)) {
                return self::mergeFloat($existingValue, $newValue);
            }
            if (is_int($newValue)) {
                return self::mergeInt($existingValue, $newValue);
            }
            if (is_bool($newValue)) {
                return self::mergeBool($existingValue, $newValue);
            }
        } catch (\TypeError $exception) {
            throw new \LogicException(
                sprintf(
                    'Type conversion from "%s" to "%s" at path "%s"',
                    is_object($existingValue) ? get_class($existingValue) : gettype($existingValue),
                    is_object($newValue) ? get_class($newValue) : gettype($newValue),
                    $path
                )
            );
        }

        throw new \LogicException(
            sprintf('Not supported data type: %s', is_object($newValue) ? get_class($newValue) : gettype($newValue))
        );
    }

    /**
     * @param array<int|string, array|bool|float|int|string|null> $existingValue
     * @param array<int|string, array|bool|float|int|string|null> $newValue
     *
     * @return array<int|string, array|bool|float|int|string|null>
     */
    private static function mergeArray(array $existingValue, array $newValue, string $path): array
    {
        foreach ($newValue as $key => $newSubValue) {
            if (!is_string($key)) {
                $existingValue[] = $newSubValue;

                continue;
            }

            $existingValue[$key] = self::merge($existingValue[$key] ?? null, $newSubValue, $path.'.'.$key);
        }

        return $existingValue;
    }

    private static function mergeString(string $existingValue, string $newValue): string
    {
        return $newValue;
    }

    private static function mergeFloat(float $existingValue, float $newValue): float
    {
        return $newValue;
    }

    private static function mergeInt(int $existingValue, int $newValue): int
    {
        return $newValue;
    }

    private static function mergeBool(bool $existingValue, bool $newValue): bool
    {
        return $newValue;
    }
}
