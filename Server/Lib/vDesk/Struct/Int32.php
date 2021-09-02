<?php
declare(strict_types=1);

namespace vDesk\Struct;

/**
 * Represents a signed 32-bit integer.
 *
 * @package vDesk
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class Int32 {

    /**
     * Represents the largest possible value of a signed 32-bit integer.
     */
    public const MaxValue = 2147483647;

    /**
     * Represents the smallest possible value of a signed 32-bit integer.
     */
    public const MinValue = -2147483648;

    /**
     * The size in Bytes a signed 32-bit integer will address.
     */
    public const Size = 4;

    /**
     * Parses a signed 32-bit integer from a specified string.
     *
     * @param string $String The string to parse.
     *
     * @return int A signed 32-bit integer that yields the value parsed from the specified string.
     * @throws \OverflowException Thrown if the numeric value of the specified string exceeds the value specific value range.
     */
    public static function ParseInt32(string $String): int {
        return Number::ParseInt32($String);
    }

    /**
     * Tries to parse a signed 32-bit integer from a specified string.
     *
     * @param string $String The string to parse.
     *
     * @return int|null A signed 32-bit integer that yields the value parsed from the specified string; otherwise, null.
     */
    public static function TryParseInt32(string $String): ?int {
        return Number::TryParseInt32($String);
    }

}
