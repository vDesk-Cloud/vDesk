<?php
declare(strict_types=1);

namespace vDesk\Struct;

/**
 * Represents an unsigned 64-bit integer.
 *
 * @package vDesk
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class UInt64 {

    /**
     * Represents the largest possible value of an unsigned 64-bit integer.
     */
    public const MaxValue = 18446744073709551615;

    /**
     * Represents the smallest possible value of an unsigned 64-bit integer.
     */
    public const MinValue = 0;

    /**
     * The size in Bytes an unsigned 64-bit integer will address.
     */
    public const Size = 8;

    /**
     * Parses an unsigned 64-bit integer from a specified string.
     *
     * @param string $String The string to parse.
     *
     * @return int An unsigned 64-bit integer that yields the value parsed from the specified string.
     * @throws \OverflowException Thrown if the numeric value of the specified string exceeds the value specific value range.
     */
    public static function Parse(string $String): int {
        return Number::ParseUInt64($String);
    }

    /**
     * Tries to parse an unsigned 64-bit integer from a specified string.
     *
     * @param string $String The string to parse.
     *
     * @return int|null An unsigned 64-bit integer that yields the value parsed from the specified string; otherwise, null.
     */
    public static function TryParse(string $String): ?int {
        return Number::TryParseUInt64($String);
    }

}
