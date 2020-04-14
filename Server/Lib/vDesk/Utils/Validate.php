<?php
declare(strict_types=1);

namespace vDesk\Utils;

use vDesk\IO\FileInfo;
use vDesk\Struct\Extension;
use vDesk\Struct\Type;

/**
 * Class Validate represents ...
 *
 * @package vDesk\Utils
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 * @version 1.0.0
 */
class Validate {
    
    /**
     * Determines whether a specified value is of a specified type.
     *
     * @param mixed             $Value     The value to validate.
     * @param string            $Type      The type of the value to validate for.
     * @param array|object|null $Validator Optional validator to validate the specified value against.
     *
     * @return bool True if the specified value is of the specified type; otherwise, false.
     */
    public static function As($Value, string $Type, $Validator = null): bool {
        
        switch($Type) {
            case Type::Int:
                return self::AsInt($Value)
                    && (
                    $Validator === null
                        ? true
                        : $Value >= ($Validator->Min ?? $Validator["Min"] ?? $Value)
                        && $Value <= ($Validator->Max ?? $Validator["Max"] ?? $Value)
                    );
            case Type::Float:
                return self::AsFloat($Value)
                    && (
                    $Validator === null
                        ? true
                        : $Value >= ($Validator->Min ?? $Validator["Min"] ?? $Value)
                        && $Value <= ($Validator->Max ?? $Validator["Max"] ?? $Value)
                    );
            case Type::String:
                return self::AsString($Value)
                    && (
                    $Validator === null
                        ? true
                        : (bool)\preg_match($Validator->Expression ?? $Validator["Expression"] ?? "/.+/", $Value)
                    );
            case Type::Array:
                return self::AsArray($Value);
            case Type::Object:
                return self::AsObject($Value);
            case Type::Bool:
                return self::AsBool($Value);
            case Type::Iterable:
                return self::AsIterable($Value);
            case Extension\Type::Enum:
                return self::AsString($Value) && \in_array($Value, $Validator);
            case Extension\Type::Color:
                return (bool)\preg_match(Expressions::ColorHexString, $Value)
                    || (bool)\preg_match(Expressions::ColorRGBString, $Value)
                    || (bool)\preg_match(Expressions::ColorRGBAString, $Value)
                    || (bool)\preg_match(Expressions::ColorHSLString, $Value)
                    || (bool)\preg_match(Expressions::ColorHSLAString, $Value);
            case Extension\Type::URL:
                return \filter_var($Value, 273) !== false;
            case Extension\Type::Email:
                return \filter_var($Value, \FILTER_VALIDATE_EMAIL) !== false;
            case Type::Mixed:
                return true;
            case Extension\Type::File:
                return $Value instanceof FileInfo;
            case Extension\Type::Money:
                return (
                    $Validator !== null
                        ? \strrchr($Value, $Validator->Currency ?? $Validator["Currency"]) !== false
                        : (bool)\preg_match(Expressions::Currency, $Value)
                    )
                    && ((float)$Value >= (float)($Validator->Min ?? $Value))
                    && ((float)$Value <= (float)($Validator->Max ?? $Value));
            case Extension\Type::Date:
            case Extension\Type::Time:
            case Extension\Type::DateTime:
                return $Value instanceof \DateTime
                    || (bool)\preg_match(Expressions::DateTimeUTC, $Value);
            case Extension\Type::TimeSpan:
                return (bool)\preg_match(Expressions::TimeSpan, $Value);
            default:
                return \class_exists($Type) ? $Value instanceof $Type : false;
        }
    }
    
    /**
     * Determines whether a specified value is an integer.
     *
     * @param mixed $Value The value to validate.
     *
     * @return bool True if the specified value is an integer; otherwise, false.
     */
    public static function AsInt($Value): bool {
        return \is_int($Value);
    }
    
    /**
     * Determines whether a specified value is a floating point number.
     *
     * @param mixed $Value The value to validate.
     *
     * @return bool True if the specified value is a floating point number; otherwise, false.
     */
    public static function AsFloat($Value): bool {
        return \is_float($Value);
    }
    
    /**
     * Determines whether a specified value is a string.
     *
     * @param mixed $Value The value to validate.
     *
     * @return bool True if the specified value is a string; otherwise, false.
     */
    public static function AsString($Value): bool {
        return \is_string($Value);
    }
    
    /**
     * Determines whether a specified value is boolean.
     *
     * @param mixed $Value The value to validate.
     *
     * @return bool True if the specified value is boolean; otherwise, false.
     */
    public static function AsBool($Value): bool {
        return \is_bool($Value);
    }
    
    /**
     * Determines whether a specified value is an array.
     *
     * @param mixed $Value The value to validate.
     *
     * @return bool True if the specified value is an array; otherwise, false.
     */
    public static function AsArray($Value): bool {
        return \is_array($Value);
    }
    
    /**
     * Determines whether a specified value is an object.
     *
     * @param mixed $Value The value to validate.
     *
     * @return bool True if the specified value is an object; otherwise, false.
     */
    public static function AsObject($Value): bool {
        return \is_object($Value);
    }
    
    /**
     * Determines whether a specified value is iterable.
     *
     * @param mixed $Value The value to validate.
     *
     * @return bool True if the specified value is iterable; otherwise, false.
     */
    public static function AsIterable($Value): bool {
        return \is_iterable($Value);
    }
    
}