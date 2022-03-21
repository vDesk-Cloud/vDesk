<?php
declare(strict_types=1);

namespace vDesk\DataProvider;

use vDesk\DataProvider\Expression\ISelect;
use vDesk\Data\IManagedModel;
use vDesk\Data\ICollectionModel;
use vDesk\Struct\Extension;
use vDesk\Struct\Type;

/**
 * Represents a static factory-class for creating database mapped getter-callback-functions to use in properties.
 *
 * @package vDesk\DataProvider
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 */
abstract class MappedGetter {
    
    /**
     * Factory method for creating database mapped getter-callbacks.
     *
     * @param mixed                                       $Property     A reference to the field or value the getter should return.
     * @param string                                      $Type         The return-type of the getter.
     * @param bool                                        $Nullable     Flag indicating whether the referenced property is nullable.
     * @param null                                        $ID           The ID the MappedGetter belongs to.
     * @param null|\vDesk\DataProvider\Expression\ISelect $Expression   The Expression to execute to retrieve the requested property's value from the database.
     * @param string                                      $Field        The field of the identifier to use in the where condition of the specified Expression.
     * @param string                                      $FieldType    The type of the field of the identifier to use in the where condition of the specified Expression.
     *
     * @return callable A generated getter.
     */
    public static function Create(
        mixed &$Property,
        string $Type,
        bool $Nullable = false,
        &$ID = null,
        ISelect $Expression = null,
        string $Field = "ID",
        string $FieldType = Type::Int
    ): callable {
        $Expression = $Expression?->Where([$Field => $ID]);
        switch($Type) {
            case Type::String:
                return $Nullable
                    ? static function() use (&$Property, &$ID, &$Expression): ?string {
                        if($Property === null && $ID !== null && $Expression !== null) {
                            $Property = match($Result = $Expression()) {
                                null => null,
                                $Result => (string)$Result
                            };
                        }
                        return $Property;
                    }
                    : static function() use (&$Property, &$ID, &$Expression): string {
                        if($Property === null && $ID !== null && $Expression !== null) {
                            $Property = (string)$Expression();
                        }
                        return $Property;
                    };
            case Type::Int:
                return $Nullable
                    ? static function() use (&$Property, &$ID, &$Expression): ?int {
                        if($Property === null && $ID !== null && $Expression !== null) {
                            $Property = match($Result = $Expression()) {
                                null => null,
                                $Result => (int)$Result
                            };
                        }
                        return $Property;
                    }
                    : static function() use (&$Property, &$ID, &$Expression): int {
                        if($Property === null && $ID !== null && $Expression !== null) {
                            $Property = (int)$Expression();
                        }
                        return $Property;
                    };
            case Type::Float:
                return $Nullable
                    ? static function() use (&$Property, &$ID, &$Expression): ?float {
                        if($Property === null && $ID !== null && $Expression !== null) {
                            $Property = match($Result = $Expression()) {
                                null => null,
                                $Result => (float)$Result
                            };
                        }
                        return $Property;
                    }
                    : static function() use (&$Property, &$ID, &$Expression): float {
                        if($Property === null && $ID !== null && $Expression !== null) {
                            $Property = (float)$Expression();
                        }
                        return $Property;
                    };
            case Type::Bool:
                return $Nullable
                    ? static function() use (&$Property, &$ID, &$Expression): ?bool {
                        if($Property === null && $ID !== null && $Expression !== null) {
                            $Property = match($Result = $Expression()) {
                                null => null,
                                $Result => (bool)$Result
                            };
                        }
                        return $Property;
                    }
                    : static function() use (&$Property, &$ID, &$Expression): bool {
                        if($Property === null && $ID !== null && $Expression !== null) {
                            $Property = (bool)$Expression();
                        }
                        return $Property;
                    };
            case Extension\Type::Time:
                return $Nullable
                    ? static function() use (&$Property, &$ID, &$Expression): ?\DateTime {
                        if($Property === null && $ID !== null && $Expression !== null) {
                            $Property = match($Result = $Expression()) {
                                null => null,
                                $Result => \DateTime::createFromFormat("H:i:s", $Result)
                            };
                        }
                        return $Property;
                    }
                    : static function() use (&$Property, &$ID, &$Expression): \DateTime {
                        if($Property === null && $ID !== null && $Expression !== null) {
                            $Property = \DateTime::createFromFormat("H:i:s", $Expression());
                        }
                        return $Property;
                    };
            case \DateTime::class:
                return $Nullable
                    ? static function() use (&$Property, &$ID, &$Expression): ?\DateTime {
                        if($Property === null && $ID !== null && $Expression !== null) {
                            $Property = match($Result = $Expression()) {
                                null => null,
                                $Result => new \DateTime($Result)
                            };
                        }
                        return $Property;
                    }
                    : static function() use (&$Property, &$ID, &$Expression): \DateTime {
                        if($Property === null && $ID !== null && $Expression !== null) {
                            $Property = new \DateTime($Expression());
                        }
                        return $Property;
                    };
            case Type::Object:
                return $Nullable
                    ? static function() use (&$Property, &$ID, &$Expression): ?object {
                        if($Property === null && $ID !== null && $Expression !== null) {
                            $Property = match($Result = $Expression()) {
                                null => null,
                                $Result => \json_decode($Result)
                            };
                        }
                        return $Property;
                    }
                    : static function() use (&$Property, &$ID, &$Expression): object {
                        if($Property === null && $ID !== null && $Expression !== null) {
                            $Property = \json_decode($Expression());
                        }
                        return $Property;
                    };
            case Type::Array:
                return $Nullable
                    ? static function() use (&$Property, &$ID, &$Expression): ?array {
                        if($Property === null && $ID !== null && $Expression !== null) {
                            $Property = match($Result = $Expression()) {
                                null => null,
                                $Result => \json_decode($Result)
                            };
                        }
                        return $Property;
                    }
                    : static function() use (&$Property, &$ID, &$Expression): array {
                        if($Property === null && $ID !== null && $Expression !== null) {
                            $Property = \json_decode($Expression());
                        }
                        return $Property;
                    };
            case Type::Mixed:
                return $Nullable
                    ? static function() use (&$Property, &$ID, &$Expression) {
                        if($Property === null && $ID !== null && $Expression !== null) {
                            $Property = $Expression();
                        }
                        return $Property;
                    }
                    : static function() use (&$Property, &$ID, &$Expression) {
                        if($Property === null && $ID !== null && $Expression !== null) {
                            $Property = $Expression();
                        }
                        if($Property === null) {
                            throw new \TypeError("Return value of getter cannot be null!");
                        }
                        return $Property;
                    };
        }
        if(\in_array(ICollectionModel::class, \class_implements($Type))) {
            return $Nullable
                ? static function() use (&$Property, &$ID, &$Expression, $Type,  $FieldType): ?ICollectionModel {
                    if($Property === null && $ID !== null && $Expression !== null) {
                        $Property = match ($FieldType) {
                            Type::Int => new $Type([], (int)$Expression()),
                            Type::Float => new $Type([], (float)$Expression()),
                            Type::String => new $Type([], $Expression())
                        };
                    }
                    return $Property;
                }
                : static function() use (&$Property, &$ID, &$Expression, $Type,  $FieldType): ICollectionModel {
                    if($Property === null && $ID !== null && $Expression !== null) {
                        $Property = match ($FieldType) {
                            Type::Int => new $Type([], (int)$Expression()),
                            Type::Float => new $Type([], (float)$Expression()),
                            Type::String => new $Type([], $Expression())
                        };
                    }
                    return $Property;
                };
        }
        return $Nullable
            ? static function() use (&$Property, &$ID, &$Expression, $Type , $FieldType): ?IManagedModel {
                if($Property === null && $ID !== null && $Expression !== null) {
                    $Property = match ($FieldType) {
                        Type::Int => new $Type((int)$Expression()),
                        Type::Float => new $Type((float)$Expression()),
                        Type::String => new $Type($Expression())
                    };
                }
                return $Property;
            }
            : static function() use (&$Property, &$ID, &$Expression, $Type , $FieldType): IManagedModel {
                if($Property === null && $ID !== null && $Expression !== null) {
                    $Property = match ($FieldType) {
                        Type::Int => new $Type((int)$Expression()),
                        Type::Float => new $Type((float)$Expression()),
                        Type::String => new $Type($Expression())
                    };
                }
                return $Property;
            };
    }
}