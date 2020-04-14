<?php
declare(strict_types=1);

namespace vDesk\Utils;

/**
 * Class Math represents ...
 *
 * @package vDesk\Utils
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 * @version 1.0.0
 */
final class Math {

    /**
     * Prevent instantiation.
     */
    private function __construct() {
    }

    /**
     * Returns the rounded value of val to specified precision (number of digits after the decimal point).
     * precision can also be negative or zero (default).
     * Note: PHP doesn't handle strings like "12,300.2" correctly by default. See converting from strings.
     *
     * @param float $Value
     * @param int   $Precision
     * @param int   $Mode
     *
     *
     * @return float
     */
    public static function Round(float $Value, int $Precision, int $Mode): float {
        return \round($Value, $Precision, $Mode);
    }

    /**
     * @param $Value
     *
     *
     * @return float
     */
    public static function Ceil($Value): float {
        return \ceil($Value);
    }
}