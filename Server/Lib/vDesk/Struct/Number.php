<?php
declare(strict_types=1);

namespace vDesk\Struct;

/**
 * Static utility class that provides functionality for working with numeric values.
 *
 * @package vDesk
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
final class Number {

    /**
     * Represents the largest possible value of a signed platform specific integer.
     */
    public const MaxValue = \PHP_INT_MAX;

    /**
     * Represents the smallest possible value of a signed platform specific integer.
     */
    public const MinValue = \PHP_INT_MIN;

    /**
     * The size in Bytes of a signed platform specific integer will address.
     */
    public const Size = \PHP_INT_SIZE;

    /**
     * Prevent instantiation.
     * Initializes a new instance of the Number class.
     */
    private function __construct() {
    }

    /**
     * Parses a signed 8-bit integer from a specified string.
     *
     * @param string $String The string to parse.
     *
     * @return int A signed 8-bit integer that yields the value parsed from the specified string.
     * @throws \OverflowException Thrown if the numeric value of the specified string exceeds the value specific value range.
     */
    public static function ParseInt8(string $String): int {
        $Value = self::ParseInt($String);
        if($Value > Int8::MaxValue || $Value < Int8::MinValue) {
            throw new \OverflowException("Parsed value exceeds possible size.");
        }
        return $Value;
    }

    /**
     * Tries to parse a signed 8-bit integer from a specified string.
     *
     * @param string $String The string to parse.
     *
     * @return int|null A signed 8-bit integer that yields the value parsed from the specified string; otherwise, null.
     */
    public static function TryParseInt8(string $String): ?int {
        $Value = self::TryParseInt($String);
        if($Value === null) {
            return $Value;
        }
        return $Value & ($Value >= 0 ? Int8::MaxValue : Int8::MinValue);
    }

    /**
     * Parses an unsigned 8-bit integer from a specified string.
     *
     * @param string $String The string to parse.
     *
     * @return int An unsigned 8-bit integer that yields the value parsed from the specified string.
     * @throws \OverflowException Thrown if the numeric value of the specified string exceeds the value specific value range.
     */
    public static function ParseUInt8(string $String): int {
        $Value = self::ParseInt($String);
        if($Value > UInt8::MaxValue || $Value < UInt8::MinValue) {
            throw new \OverflowException("Parsed value exceeds possible size.");
        }
        return $Value;
    }

    /**
     * Tries to parse an unsigned 8-bit integer from a specified string.
     *
     * @param string $String The string to parse.
     *
     * @return int|null An unsigned 8-bit integer that yields the value parsed from the specified string; otherwise, null.
     */
    public static function TryParseUInt8(string $String): ?int {
        $Value = self::TryParseInt($String);
        if($Value === null) {
            return $Value;
        }
        return $Value & ($Value >= 0 ? UInt8::MaxValue : UInt8::MinValue);
    }

    /**
     * Parses a signed 16-bit integer from a specified string.
     *
     * @param string $String The string to parse.
     *
     * @return int A signed 16-bit integer that yields the value parsed from the specified string.
     * @throws \OverflowException Thrown if the numeric value of the specified string exceeds the value specific value range.
     */
    public static function ParseInt16(string $String): int {
        $Value = self::ParseInt($String);
        if($Value > Int16::MaxValue || $Value < Int16::MinValue) {
            throw new \OverflowException("Parsed value exceeds possible size.");
        }
        return $Value;
    }

    /**
     * Tries to parse a signed 16-bit integer from a specified string.
     *
     * @param string $String The string to parse.
     *
     * @return int|null A signed 16-bit integer that yields the value parsed from the specified string; otherwise, null.
     */
    public static function TryParseInt16(string $String): ?int {
        $Value = self::TryParseInt($String);
        if($Value === null) {
            return $Value;
        }
        return $Value & ($Value >= 0 ? Int16::MaxValue : Int16::MinValue);
    }

    /**
     * Parses an unsigned 16-bit integer from a specified string.
     *
     * @param string $String The string to parse.
     *
     * @return int An unsigned 16-bit integer that yields the value parsed from the specified string.
     * @throws \OverflowException Thrown if the numeric value of the specified string exceeds the value specific value range.
     */
    public static function ParseUInt16(string $String): int {
        $Value = self::ParseInt($String);
        if($Value > UInt16::MaxValue || $Value < UInt16::MinValue) {
            throw new \OverflowException("Parsed value exceeds possible size.");
        }
        return $Value;
    }

    /**
     * Tries to parse an unsigned 16-bit integer from a specified string.
     *
     * @param string $String The string to parse.
     *
     * @return int|null An unsigned 16-bit integer that yields the value parsed from the specified string; otherwise, null.
     */
    public static function TryParseUInt16(string $String): ?int {
        $Value = self::TryParseInt($String);
        if($Value === null) {
            return $Value;
        }
        return $Value & ($Value >= 0 ? UInt16::MaxValue : UInt16::MinValue);
    }

    /**
     * Parses a signed 32-bit integer from a specified string.
     *
     * @param string $String The string to parse.
     *
     * @return int A signed 32-bit integer that yields the value parsed from the specified string.
     * @throws \OverflowException Thrown if the numeric value of the specified string exceeds the value specific value range.
     */
    public static function ParseInt32(string $String): int {
        $Value = self::ParseInt($String);
        if($Value > Int32::MaxValue || $Value < Int32::MinValue) {
            throw new \OverflowException("Parsed value exceeds possible size.");
        }
        return $Value;
    }

    /**
     * Tries to parse a signed 32-bit integer from a specified string.
     *
     * @param string $String The string to parse.
     *
     * @return int|null A signed 32-bit integer that yields the value parsed from the specified string; otherwise, null.
     */
    public static function TryParseInt32(string $String): ?int {
        $Value = self::TryParseInt($String);
        if($Value === null) {
            return $Value;
        }
        return $Value & ($Value >= 0 ? Int32::MaxValue : Int32::MinValue);
    }

    /**
     * Parses an unsigned 32-bit integer from a specified string.
     *
     * @param string $String The string to parse.
     *
     * @return int An unsigned 32-bit integer that yields the value parsed from the specified string.
     * @throws \OverflowException Thrown if the numeric value of the specified string exceeds the value specific value range.
     */
    public static function ParseUInt32(string $String): int {
        $Value = self::ParseInt($String);
        if($Value > UInt32::MaxValue || $Value < UInt32::MinValue) {
            throw new \OverflowException("Parsed value exceeds possible size.");
        }
        return $Value;
    }

    /**
     * Tries to parse an unsigned 32-bit integer from a specified string.
     *
     * @param string $String The string to parse.
     *
     * @return int|null An unsigned 32-bit integer that yields the value parsed from the specified string; otherwise, null.
     */
    public static function TryParseUInt32(string $String): ?int {
        $Value = self::TryParseInt($String);
        if($Value === null) {
            return $Value;
        }
        return $Value & ($Value >= 0 ? UInt32::MaxValue : UInt32::MinValue);
    }

    /**
     * Parses a signed 64-bit integer from a specified string.
     *
     * @param string $String The string to parse.
     *
     * @return int A signed 64-bit integer that yields the value parsed from the specified string.
     * @throws \OverflowException Thrown if the numeric value of the specified string exceeds the value specific value range.
     */
    public static function ParseInt64(string $String): int {
        $Value = self::ParseInt($String);
        if($Value > Int64::MaxValue || $Value < Int64::MinValue) {
            throw new \OverflowException("Parsed value exceeds possible size.");
        }
        return $Value;
    }

    /**
     * Tries to parse a signed 64-bit integer from a specified string.
     *
     * @param string $String The string to parse.
     *
     * @return int|null A signed 64-bit integer that yields the value parsed from the specified string; otherwise, null.
     */
    public static function TryParseInt64(string $String): ?int {
        $Value = self::TryParseInt($String);
        if($Value === null) {
            return $Value;
        }
        return $Value & ($Value >= 0 ? Int64::MaxValue : Int64::MinValue);
    }

    /**
     * Parses an unsigned 64-bit integer from a specified string.
     *
     * @param string $String The string to parse.
     *
     * @return int An unsigned 64-bit integer that yields the value parsed from the specified string.
     * @throws \OverflowException Thrown if the numeric value of the specified string exceeds the value specific value range.
     */
    public static function ParseUInt64(string $String): int {
        $Value = self::ParseInt($String);
        if($Value > UInt64::MaxValue || $Value < UInt64::MinValue) {
            throw new \OverflowException("Parsed value exceeds possible size.");
        }
        return $Value;
    }

    /**
     * Tries to parse an unsigned 64-bit integer from a specified string.
     *
     * @param string $String The string to parse.
     *
     * @return int|null An unsigned 64-bit integer that yields the value parsed from the specified string; otherwise, null.
     */
    public static function TryParseUInt64(string $String): ?int {
        $Value = self::TryParseInt($String);
        if($Value === null) {
            return $Value;
        }
        return $Value & ($Value >= 0 ? UInt64::MaxValue : UInt64::MinValue);
    }

    /**
     * Determines whether the specified value is of the type int or float a numeric string.
     *
     * @param mixed $Value The value to check.
     *
     * @return bool True if the specified value is numeric; otherwise, false.
     */
    public static function IsNumeric($Value): bool {
        return \is_numeric($Value);
    }

    /**
     * Generates a random number in a specified range.
     *
     * @param int $Min The minimum value the random number will have.
     * @param int $Max The maximum value the random number will have.
     *
     * @return int A random signed 64 bit integer within the specified range.
     */
    public static function Random(int $Min = self::MinValue, int $Max = self::MaxValue): int {
        return \random_int($Min, $Max);
    }

    /**
     * Parses an integer from a specified string.
     *
     * @param string $String The string to parse.
     *
     * @return int An integer that yields the value parsed from the specified string.
     */
    private static function ParseInt(string $String): int {
        if(!\is_numeric($String)) {
            throw new \InvalidArgumentException("The specified string doesn't contain any valid numeric characters.");
        }
        return (int)$String;
    }

    /**
     * Tries to parse an integer from a specified string.
     *
     * @param string $String The string to parse.
     *
     * @return int|null An integer that yields the value parsed from the specified string; otherwise, null.
     */
    private static function TryParseInt(string $String): ?int {
        return \is_numeric($String) ? (int)$String : null;
    }

    /**
     * Parses a float from a specified string.
     *
     * @param string $String The string to parse.
     *
     * @return float A floating point value that yields the value parsed from the specified string.
     */
    private static function ParseFloat(string $String): float {
        if(!\is_numeric($String)) {
            throw new \InvalidArgumentException("The specified string doesn't contain any valid numeric characters.");
        }
        return (float)$String;
    }

    /**
     * Tries to parse a float from a specified string.
     *
     * @param string $String The string to parse.
     *
     * @return float|null A floating point value that yields the value parsed from the specified string; otherwise, null.
     */
    private static function TryParseFloat(string $String): ?float {
        return \is_numeric($String) ? (float)$String : null;
    }

}
