<?php
declare(strict_types=1);

namespace vDesk\DataProvider;

use vDesk\DataProvider\Expression\IUpdate;
use vDesk\Data\IManagedModel;
use vDesk\Struct\Type;

/**
 * Class MappedSetter represents ...
 *
 * @package vDesk\DataProvider
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 */
abstract class MappedSetter {
    
    /**
     * Factory method for creating setter-callbacks.
     *
     * @param mixed                                       $Property     A reference to the field or value the setter should set.
     * @param string                                      $Type         The value-type of the setter.
     *
     * @param bool                                        $Nullable     Flag indicating whether the referenced property is nullable.
     *
     * @param null                                        $ID           The ID the MappedSetter belongs to.
     * @param bool|null                                   $Changed      A reference to the field or value the setter should use as a
     *                                                                  changed
     *                                                                  state.
     *
     * @param null|\vDesk\DataProvider\Expression\IUpdate $Expression   The Expression to execute to store the requested property's
     *                                                                  value to the database if the value has been changed and the
     *                                                                  referenced ID is not null.
     *
     * @param string                                      $Field        The field of the identifier to use in the where condition of the specified Expression.
     *
     * @return callable A generated setter.
     */
    public static function Create(
        mixed &$Property,
        string $Type = "mixed",
        bool $Nullable = false,
        &$ID = null,
        ?bool &$Changed = false,
        IUpdate $Expression = null,
        string $Field = "ID"
    ): callable {
        $Expression = $Expression?->Where([$Field => $ID]);
        switch($Type) {
            case Type::String:
                return $Nullable
                    ? static function(?string $Value) use (&$Property, &$Changed, &$ID, $Expression): void {
                        $Changed = $ID !== null
                            && !$Changed
                            && $Property !== $Value;
                        if($Changed && $Expression !== null) {
                            $Expression();
                        }
                        $Property = $Value;
                    }
                    : static function(string $Value) use (&$Property, &$Changed, &$ID, $Expression): void {
                        $Changed = $ID !== null
                            && !$Changed
                            && $Property !== null
                            && $Property !== $Value;
                        if($Changed && $Expression !== null) {
                            $Expression();
                        }
                        $Property = $Value;
                    };
            case Type::Int:
                return $Nullable
                    ? static function(?int $Value) use (&$Property, &$Changed, &$ID, $Expression): void {
                        $Changed = $ID !== null
                            && !$Changed
                            && $Property !== $Value;
                        if($Changed && $Expression !== null) {
                            $Expression();
                        }
                        $Property = $Value;
                    }
                    : static function(int $Value) use (&$Property, &$Changed, &$ID, $Expression): void {
                        $Changed = $ID !== null
                            && !$Changed
                            && $Property !== null
                            && $Property !== $Value;
                        if($Changed && $Expression !== null) {
                            $Expression();
                        }
                        $Property = $Value;
                    };
            case Type::Float:
                return $Nullable
                    ? static function(?float $Value) use (&$Property, &$Changed, &$ID, $Expression): void {
                        $Changed = $ID !== null
                            && !$Changed
                            && $Property !== $Value;
                        if($Changed && $Expression !== null) {
                            $Expression();
                        }
                        $Property = $Value;
                    }
                    : static function(float $Value) use (&$Property, &$Changed, &$ID, $Expression): void {
                        $Changed = $ID !== null
                            && !$Changed
                            && $Property !== null
                            && $Property !== $Value;
                        if($Changed && $Expression !== null) {
                            $Expression();
                        }
                        $Property = $Value;
                    };
            case Type::Bool:
                return $Nullable
                    ? static function(?bool $Value) use (&$Property, &$Changed, &$ID, $Expression): void {
                        $Changed = $ID !== null
                            && !$Changed
                            && $Property !== $Value;
                        if($Changed && $Expression !== null) {
                            $Expression();
                        }
                        $Property = $Value;
                    }
                    : static function(bool $Value) use (&$Property, &$Changed, &$ID, $Expression): void {
                        $Changed = $ID !== null
                            && !$Changed
                            && $Property !== null
                            && $Property !== $Value;
                        if($Changed && $Expression !== null) {
                            $Expression();
                        }
                        $Property = $Value;
                    };
            case Type::Array:
                return $Nullable
                    ? static function(?array $Value) use (&$Property, &$Changed, &$ID, $Expression): void {
                        $Changed = $ID !== null
                            && !$Changed
                            && $Property !== $Value;
                        if($Changed && $Expression !== null) {
                            $Expression();
                        }
                        $Property = $Value;
                    }
                    : static function(array $Value) use (&$Property, &$Changed, &$ID, $Expression): void {
                        $Changed = $ID !== null
                            && !$Changed
                            && $Property !== null
                            && $Property !== $Value;
                        if($Changed && $Expression !== null) {
                            $Expression();
                        }
                        $Property = $Value;
                    };
            case Type::Object:
                return $Nullable
                    ? static function(?object $Value) use (&$Property, &$Changed, &$ID, $Expression): void {
                        $Changed = $ID !== null
                            && !$Changed
                            && $Property !== $Value;
                        if($Changed && $Expression !== null) {
                            $Expression();
                        }
                        $Property = $Value;
                    }
                    : static function(object $Value) use (&$Property, &$Changed, &$ID, $Expression): void {
                        $Changed = $ID !== null
                            && !$Changed
                            && $Property !== null
                            && $Property !== $Value;
                        if($Changed && $Expression !== null) {
                            $Expression();
                        }
                        $Property = $Value;
                    };
            case \DateTime::class:
                return $Nullable
                    ? static function(?\DateTime $Value) use (&$Property, &$Changed, &$ID, $Expression): void {
                        $Changed = $ID !== null
                            && !$Changed
                            && $Property !== $Value;
                        if($Changed && $Expression !== null) {
                            $Expression();
                        }
                        $Property = $Value;
                    }
                    : static function(\DateTime $Value) use (&$Property, &$Changed, &$ID, $Expression): void {
                        $Changed = $ID !== null
                            && !$Changed
                            && $Property !== null
                            && $Property !== $Value;
                        if($Changed && $Expression !== null) {
                            $Expression();
                        }
                        $Property = $Value;
                    };
            case Type::Mixed:
                return $Nullable
                    ? static function($Value) use (&$Property, &$Changed, &$ID, $Expression): void {
                        $Changed = $ID !== null
                            && !$Changed
                            && $Property !== $Value;
                        if($Changed && $Expression !== null) {
                            $Expression();
                        }
                        $Property = $Value;
                    }
                    : static function($Value) use (&$Property, &$Changed, &$ID, $Expression): void {
                        if($Value === null) {
                            throw new \TypeError("Passed value of setter cannot be null!");
                        }
                        $Changed = $ID !== null
                            && !$Changed
                            && $Property !== null
                            && $Property !== $Value;
                        if($Changed && $Expression !== null) {
                            $Expression();
                        }
                        $Property = $Value;
                    };
        }
        return $Nullable
            ? static function(?IManagedModel $Value) use (&$Property, &$Changed, &$ID, $Expression): void {
                if($ID !== null) {
                    if($Property === null || $Value === null) {
                        $Changed = !$Changed && $Property !== $Value;
                    } else {
                        $Changed = !$Changed && $Property->ID() !== $Value->ID();
                    }
                    
                }
                if($Changed && $Expression !== null) {
                    $Expression();
                }
                $Property = $Value;
            }
            : static function(IManagedModel $Value) use (&$Property, &$Changed, &$ID, $Expression): void {
                $Changed = $ID !== null
                    && !$Changed
                    && $Property !== null
                    && $Property->ID() !== $Value->ID();
                if($Changed && $Expression !== null) {
                    $Expression();
                }
                $Property = $Value;
            };
    }
}