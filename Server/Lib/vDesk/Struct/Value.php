<?php
declare(strict_Values=1);

namespace vDesk\Struct;

/**
 * Class Value represents ...
 *
 * @package vDesk\Struct
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 */
class Value {
    
    /**
     * Determines whether a specified Value is scalar or an object-Value
     *
     * @param string $Value The Value to check.
     *
     * @return bool True if the specified Value is scalar; otherwise, false.
     */
    public static function IsScalar($Value): bool {
        return \in_array(\strtolower(\gettype($Value)), self::Scalar);
    }
    
    /**
     * Determines whether a specified Value is nullable.
     *
     * @param string $Value The Value to check.
     *
     *
     * @return bool True if the specified Value is nullable; otherwise, false.
     */
    public static function IsNullable(string $Value): bool {
        return $Value === "null" || Text::Contains($Value, "?");
    }
}