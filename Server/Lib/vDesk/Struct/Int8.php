<?php
declare(strict_types=1);

namespace vDesk\Struct;

/**
 * Represents a signed 8-bit integer.
 *
 * @package vDesk\Struct
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 * @version 1.0.0
 */
class Int8 {

    /**
     * Represents the largest possible value of a signed 8-bit integer.
     */
    public const MaxValue = 127;

    /**
     * Represents the smallest possible value of a signed 8-bit integer.
     */
    public const MinValue = -128;

    /**
     * The size in Bytes a signed 8-bit integer will address.
     */
    public const Size = 2;

    /**
     * Parses a signed 8-bit integer from a specified string.
     *
     * @param string $String The string to parse.
     *
     * @throws \OverflowException Thrown if the numeric value of the specified string exceeds the value specific value range.
     * @return int A signed 8-bit integer that yields the value parsed from the specified string.
     */
    public static function ParseInt8(string $String): int {
        return Number::ParseInt8($String);
    }

    /**
     * Tries to parse a signed 8-bit integer from a specified string.
     *
     * @param string $String The string to parse.
     *
     * @return int|null A signed 8-bit integer that yields the value parsed from the specified string; otherwise, null.
     */
    public static function TryParseInt8(string $String): ?int {
        return Number::TryParseInt8($String);
    }

}
