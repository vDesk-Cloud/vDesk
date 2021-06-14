<?php
declare(strict_types=1);

namespace vDesk\Crash;

/**
 * Utility class that provides functionality for generating random values.
 *
 * @package vDesk\Crash
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class Random {

    /**
     * Text values for random value generation.
     */
    public const Text = ["Lorem", "Ipsum", "Dolor", "Sit", "Amet"];

    /**
     * Generates a random value.
     *
     * @return mixed A random value of any type.
     */
    public static function Value(): mixed {
        return match (\random_int(0, 5)) {
            0 => static::Int(true),
            1 => static::Float(true),
            2 => static::Char(),
            3 => static::String(),
            4 => static::Bool(),
            5 => static::Array()
        };
    }

    /**
     * Generates a numeric array of random values.
     *
     * @param null|int $Amount Optional amount of random values.
     *
     * @return array A numeric array of random values.
     */
    public static function Values(int $Amount = null): array {
        return \array_map(static fn() => static::Value(), \range(0, $Amount ?? \random_int(1, 10)));
    }

    /**
     * Generates a random integer.
     *
     * @param bool $Negative $Flag indicating whether to allow negative values.
     *
     * @return int A random integer.
     */
    public static function Int(bool $Negative = false): int {
        if($Negative) {
            return \random_int(\PHP_INT_MAX * -1, \PHP_INT_MAX);
        }
        return \random_int(0, \PHP_INT_MAX);
    }


    /**
     * Generates a random float.
     *
     * @param bool $Negative $Flag indicating whether to allow negative values.
     *
     * @return float A random float.
     */
    public static function Float(bool $Negative = false): float {
        return static::Int($Negative) / \time();
    }

    /**
     * Generates a random boolean value.
     *
     * @return bool A random bool.
     */
    public static function Bool(): bool {
        return (bool)\random_int(0, 1);
    }

    /**
     * Generates a random string.
     *
     * @return string A random string.
     */
    public static function String(): string {
        return match (\random_int(0, 4)) {
            0 => static::Text[\random_int(0, \count(static::Text) - 1)],
            1 => \implode(
                "",
                \array_map(
                    static fn($Index) => static::Text[$Index],
                    \array_rand(
                        static::Text,
                        \random_int(2, \count(static::Text) - 1)
                    )
                )
            ),
            2 => \implode(
                "",
                \array_map(
                    static fn() => static::Char(),
                    \range(0, \random_int(2, 60))
                )
            ),
            3 => \implode(static::Char(), static::Text),
            4 => \str_shuffle(static::String())
        };
    }

    /**
     * Generates a random upper- or lowercase alphabetical character.
     *
     * @return string A random char.
     */
    public static function Char(): string {
        return match (\random_int(0, 1)) {
            0 => \chr(\random_int(65, 90)),
            1 => \chr(\random_int(97, 122))
        };
    }

    /**
     * Generates an array.
     *
     * @return array A random array.
     */
    public static function Array(): array {
        return match (\random_int(0, 5)) {
            0 => \range(0, \random_int(1, 15)),
            1 => match (\random_int(0, 1)) {
                0 => static::Text,
                1 => \array_reverse(static::Text)
            },
            2 => \array_map(
                static fn() => match (\random_int(0, 5)) {
                    0 => static::Int(true),
                    1 => static::Float(true),
                    2 => static::String(),
                    3 => static::Char(),
                    4 => static::Bool(),
                    5 => static::Array()
                },
                \range(0, \random_int(3, 10))
            ),
            3 => \array_map(static fn() => static::String(), \range(0, \random_int(1, 15))),
            4 => \array_map(static fn() => static::Float(), \range(0, \random_int(1, 15))),
            5 => \array_map(static fn() => static::Int(), \range(0, \random_int(1, 15)))
        };
    }

}