<?php
declare(strict_types=1);

namespace vDesk\Struct\Extension;

use vDesk\IO\FileInfo;
use vDesk\Utils\Expressions;

/**
 * Enumeration of extension types.
 *
 * @package vDesk
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class Type {
    
    /**
     * date
     */
    public const Date = "date";
    
    /**
     * time
     */
    public const Time = "time";
    
    /**
     * datetime
     */
    public const DateTime = self::Date . self::Time;
    
    /**
     * color
     */
    public const Color = "color";
    
    /**
     * url
     */
    public const URL = "url";
    
    /**
     * email
     */
    public const Email = "email";
    
    /**
     * money
     */
    public const Money = "money";
    
    /**
     * file
     */
    public const File = "file";
    
    /**
     * timespan
     */
    public const TimeSpan = "timespan";
    
    /**
     * enum
     */
    public const Enum = "enum";
    
    /**
     * map (JavaScript object to PHP array)
     */
    public const Map = "map";
    
    /**
     * Determines whether a specified type is an extension type.
     *
     * @param string $Type The type to check.
     *
     *
     * @return bool True if the specified type is an extension type; otherwise, false.
     */
    public static function IsExtensionType(string $Type): bool {
        return match ($Type) {
            self::Date, self::Time, self::DateTime, self::Color, self::URL, self::Email, self::Money, self::File, self::TimeSpan, self::Enum => true,
            default => false,
        };
    }
    
    /**
     * Determines the type of a specified value.
     *
     * @param $Value mixed The value to determine its type of.
     *
     * @return string The name of the type of the specified value.
     */
    public static function Of(mixed $Value): string {
        
        if($Value instanceof \DateTime) {
            return self::DateTime;
        }
        if($Value instanceof FileInfo) {
            return self::File;
        }
        
        if(\is_string($Value)) {
            if(\filter_var($Value, 273) !== false) {
                return self::URL;
            }
            if(\filter_var($Value, 274) !== false) {
                return self::Email;
            }
            if(
                (bool)\preg_match(Expressions::ColorHexString, $Value)
                || (bool)\preg_match(Expressions::ColorRGBString, $Value)
                || (bool)\preg_match(Expressions::ColorRGBAString, $Value)
                || (bool)\preg_match(Expressions::ColorHSLString, $Value)
                || (bool)\preg_match(Expressions::ColorHSLAString, $Value)
            ) {
                return self::Color;
            }
            if((bool)\preg_match(Expressions::Money, $Value)) {
                return self::Money;
            }
            if((bool)\preg_match(Expressions::Money, $Value)) {
                return self::Email;
            }
        }
        
        return \vDesk\Struct\Type::Of($Value);
        
    }
    
}