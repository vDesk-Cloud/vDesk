<?php
declare(strict_types=1);

namespace vDesk\Utils;

/**
 * Description of Expressions
 *
 * @author Kerry
 */
abstract class Expressions {

    /**
     * UTC-DateTime format.
     */
    public const DateTimeUTC = "/^(\d{4})-(\d{2})-(\d{2})T(\d{2}):(\d{2}):(\d{2})(\.\d{3})Z$/";

    /**
     * Money format.
     */
    public const Money = "/(?:(^\d{1,})|(\d{1,})(?:\.?|\,?)(\d{2}))[€\$£¥₽₿]\$/u";
    
    /**
     * Currency character.
     */
    public const Currency = "/[€\$£¥₽₿]\$/u";

    /**
     * Timespan format.
     */
    public const TimeSpan = "/^([0-9]{2,3})\:([0-5][0-9])\:([0-5][0-9])$/";

    /**
     * Checks if a string is a valid hexadecimal color string.
     */
    public const ColorHexString = "/#([0-9a-fA-F]{2})([0-9a-fA-F]{2})([0-9a-fA-F]{2})/";

    /**
     * Checks if a string is a valid RGB color string.
     */
    public const ColorRGBString = "/^rgb\((\d{1,3})\,\s?(\d{1,3})\,\s?(\d{1,3})\)$/";

    /**
     * Checks if a string is a valid RGBA color string.
     */
    public const ColorRGBAString = "/^rgba\((\d{1,3})\,\s?(\d{1,3})\,\s?(\d{1,3})\,\s?(1|0?\.\d+)\)$/";

    /**
     * Checks if a string is a valid HSL color string.
     */
    public const ColorHSLString = "/^hsl\((\d{1,3})\,\s?(\d{1,3})\,\s?(\d{1,3})\)$/";

    /**
     * Checks if a string is a valid HSLA color string.
     */
    public const ColorHSLAString = "/^hsla\((\d{1,3})\,\s?(\d{1,3})\,\s?(\d{1,3})\,\s?(1|0?\.\d+)\)$/";

}
