<?php
declare(strict_types=1);

namespace vDesk\Struct;

/**
 * Represents a signed 64-bit integer.
 *
 * @package vDesk\Struct
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 */
class Int64 {
    
    /**
     * Represents the largest possible value of a signed 64-bit integer.
     */
    public const MaxValue = 9223372036854775807;
    
    /**
     * Represents the smallest possible value of a signed 64-bit integer.
     */
    public const MinValue = -9223372036854775808;
    
    /**
     * The size in Bytes a signed 64-bit integer will address.
     */
    public const Size = 8;
    
    /**
     * Parses a signed 64-bit integer from a specified string.
     *
     * @param string $String The string to parse.
     *
     * @return int A signed 64-bit integer that yields the value parsed from the specified string.
     * @throws \OverflowException Thrown if the numeric value of the specified string exceeds the value specific value range.
     */
    public static function ParseInt64(string $String): int {
        return Number::ParseInt64($String);
    }
    
    /**
     * Tries to parse a signed 64-bit integer from a specified string.
     *
     * @param string $String The string to parse.
     *
     * @return int|null A signed 64-bit integer that yields the value parsed from the specified string; otherwise, null.
     */
    public static function TryParseInt64(string $String): ?int {
        return Number::TryParseInt64($String);
    }
}
