<?php
declare(strict_types=1);

namespace vDesk\Struct\Property;

use vDesk\Struct\Type;

/**
 * Represents a static factory-class for creating setter-callback-functions to use in properties.
 *
 * @package vDesk\Struct\Property
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 * @version 1.0.0
 */
abstract class Setter {

    /**
     * Factory method for creating setter-callbacks.
     *
     * @param mixed  $Property A reference to the field or value the setter should set.
     * @param string $Type     The value-type of the setter.
     * @param bool   $Nullable Flag indicating whether the referenced property is nullable.
     * @param bool   $Changed  A reference to the field or value the setter should use as a changed state.
     *
     * @return callable A generated setter.
     */
    public static function Create(&$Property, string $Type = Type::Mixed, bool $Nullable = false, ?bool &$Changed = false): callable {
        switch($Type) {
            case Type::String:
                return $Nullable
                    ? static function(?string $Value) use (&$Property, &$Changed): void {
                        $Changed  = $Value !== null && !$Changed && $Property !== $Value;
                        $Property = $Value;
                    }
                    : static function(string $Value) use (&$Property, &$Changed): void {
                        $Changed  = !$Changed && $Property !== $Value;
                        $Property = $Value;
                    };
            case Type::Int:
                return $Nullable
                    ? static function(?int $Value) use (&$Property, &$Changed): void {
                        $Changed  = $Value !== null && !$Changed && $Property !== $Value;
                        $Property = $Value;
                    }
                    : static function(int $Value) use (&$Property, &$Changed): void {
                        $Changed  = !$Changed && $Property !== $Value;
                        $Property = $Value;
                    };
            case Type::Float:
                return $Nullable
                    ? static function(?float $Value) use (&$Property, &$Changed): void {
                        $Changed  = $Value !== null && !$Changed && $Property !== $Value;
                        $Property = $Value;
                    }
                    : static function(float $Value) use (&$Property, &$Changed): void {
                        $Changed  = !$Changed && $Property !== $Value;
                        $Property = $Value;
                    };
            case Type::Bool:
                return $Nullable
                    ? static function(?bool $Value) use (&$Property, &$Changed): void {
                        $Changed  = $Value !== null && !$Changed && $Property !== $Value;
                        $Property = $Value;
                    }
                    : static function(bool $Value) use (&$Property, &$Changed): void {
                        $Changed  = !$Changed && $Property !== $Value;
                        $Property = $Value;
                    };
            case Type::Array:
                return $Nullable
                    ? static function(?array $Value) use (&$Property, &$Changed): void {
                        $Changed  = $Value !== null && !$Changed && $Property !== $Value;
                        $Property = $Value;
                    }
                    : static function(array $Value) use (&$Property, &$Changed): void {
                        $Changed  = !$Changed && $Property !== $Value;
                        $Property = $Value;
                    };
            case Type::Object:
                return $Nullable
                    ? static function(?object $Value) use (&$Property, &$Changed): void {
                        $Changed  = $Value !== null && !$Changed && $Property !== $Value;
                        $Property = $Value;
                    }
                    : static function(object $Value) use (&$Property, &$Changed): void {
                        $Changed  = !$Changed && $Property !== $Value;
                        $Property = $Value;
                    };
        }
        // Default setter.
        return static function($Value) use (&$Property, &$Changed, $Type, $Nullable): void {
            if($Nullable && $Value !== null && !$Value instanceof $Type) {
                throw Type::Error("Value", $Type, $Value);
            }
            $Changed  = !$Changed && $Property !== $Value;
            $Property = $Value;
        };
    }
}