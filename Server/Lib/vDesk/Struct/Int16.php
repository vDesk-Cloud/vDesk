<?php
declare(strict_types=1);

namespace vDesk\Struct;

/**
 * Represents a signed 16-bit integer.
 *
 * @package vDesk\Struct
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 * @version 1.0.0
 */
class Int16 {

    /**
     * Represents the largest possible value of a signed 16-bit integer.
     */
    public const MaxValue = 32767;

    /**
     * Represents the smallest possible value of a signed 16-bit integer.
     */
    public const MinValue = -32768;

    /**
     * The size in Bytes a signed 16-bit integer will address.
     */
    public const Size = 2;

    /**
     * Parses a signed 16-bit integer from a specified string.
     *
     * @param string $String The string to parse.
     *
     * @throws \OverflowException Thrown if the numeric value of the specified string exceeds the value specific value range.
     * @return int A signed 16-bit integer that yields the value parsed from the specified string.
     */
    public static function ParseInt16(string $String): int {
        return Number::ParseInt16($String);
    }

    /**
     * Tries to parse a signed 16-bit integer from a specified string.
     *
     * @param string $String The string to parse.
     *
     * @return int|null A signed 16-bit integer that yields the value parsed from the specified string; otherwise, null.
     */
    public static function TryParseInt16(string $String): ?int {
        return Number::TryParseInt16($String);
    }

}
