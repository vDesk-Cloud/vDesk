<?php
declare(strict_types=1);

namespace vDesk\Utils;

use vDesk\Struct\Number;
use vDesk\Struct\Type;

/**
 * Collection of static utility methods to convert a desired type into a different type.
 *
 * @package vDesk\utils
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 */
final class Convert {
    
    /**
     * Converts a specified value into its representation as a signed 8-bit integer.
     *
     * @param mixed $Value The value to convert.
     *
     * @return int An integer that represents the specified value.
     */
    public static function ToInt8(mixed $Value): int {
        return Number::TryParseInt8($Value) ?? 0;
    }
    
    /**
     * Converts a specified value into its representation as an unsigned 8-bit integer.
     *
     * @param mixed $Value The value to convert.
     *
     * @return int An integer that represents the specified value.
     */
    public static function ToUInt8(mixed $Value): int {
        return Number::TryParseUInt8($Value) ?? 0;
    }
    
    /**
     * Converts a specified value into its representation as an unsigned 8-bit integer.
     *
     * @param mixed $Value The value to convert.
     *
     * @return int An integer that represents the specified value.
     * @see \vDesk\Utils\Convert::ToInt8()
     *
     */
    public static function ToByte(mixed $Value): int {
        return self::ToInt8($Value);
    }
    
    /**
     * Converts a specified value into its representation as a signed 16-bit integer.
     *
     * @param mixed $Value The value to convert.
     *
     * @return int An integer that represents the specified value.
     */
    public static function ToInt16(mixed $Value): int {
        return Number::TryParseInt16($Value) ?? 0;
    }
    
    /**
     * Converts a specified value into its representation as an unsigned 16-bit integer.
     *
     * @param mixed $Value The value to convert.
     *
     * @return int An integer that represents the specified value.
     */
    public static function ToUInt16(mixed $Value): int {
        return Number::TryParseUInt16($Value) ?? 0;
    }
    
    /**
     * Converts a specified value into its representation as an unsigned 16-bit integer.
     *
     * @param mixed $Value The value to convert.
     *
     * @return int An integer that represents the specified value.
     * @see \vDesk\Utils\Convert::ToInt16()
     *
     */
    public static function ToShort(mixed $Value): int {
        return self::ToInt16($Value);
    }
    
    /**
     * Converts a specified value into its representation as a signed 32-bit integer.
     *
     * @param mixed $Value The value to convert.
     *
     * @return int An integer that represents the specified value.
     */
    public static function ToInt32(mixed $Value): int {
        return Number::TryParseInt32($Value) ?? 0;
    }
    
    /**
     * Converts a specified value into its representation as an unsigned 32-bit integer.
     *
     * @param mixed $Value The value to convert.
     *
     * @return int An integer that represents the specified value.
     */
    public static function ToUInt32(mixed $Value): int {
        return Number::TryParseUInt32($Value) ?? 0;
    }
    
    /**
     * Converts a specified value into its representation as an unsigned 32-bit integer.
     *
     * @param mixed $Value The value to convert.
     *
     * @return int An integer that represents the specified value.
     * @see \vDesk\Utils\Convert::ToInt32()
     *
     */
    public static function ToInt(mixed $Value): int {
        return self::ToInt32($Value);
    }
    
    /**
     * Converts a specified value into its representation as a signed 64-bit integer.
     *
     * @param mixed $Value The value to convert.
     *
     * @return int An integer that represents the specified value.
     */
    public static function ToInt64(mixed $Value): int {
        return Number::TryParseInt64($Value) ?? 0;
    }
    
    /**
     * Converts a specified value into its representation as an unsigned 64-bit integer.
     *
     * @param mixed $Value The value to convert.
     *
     * @return int An integer that represents the specified value.
     */
    public static function ToUInt64(mixed $Value): int {
        return Number::TryParseUInt64($Value) ?? 0;
    }
    
    /**
     * Converts a specified value into its representation as an unsigned 64-bit integer.
     *
     * @param mixed $Value The value to convert.
     *
     * @return int An integer that represents the specified value.
     * @see \vDesk\Utils\Convert::ToInt64()
     *
     */
    public static function ToLong(mixed $Value): int {
        return self::ToInt64($Value);
    }
    
    /**
     * Converts a specified value into a specified type.
     *
     * @param string $Type  The type to convert to value to.
     * @param mixed  $Value The value to convert.
     *
     * @return bool|float|int|string The converted value.
     */
    public static function To(string $Type, mixed $Value): mixed {
        return match ($Type) {
            Type::Int => (int)$Value,
            Type::String => (string)$Value,
            Type::Bool => (bool)$Value,
            Type::Float => (float)$Value,
            default => $Value
        };
    }
    
}