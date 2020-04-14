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
 * @version 1.0.0
 */
abstract class MappedGetter {

    /**
     * Factory method for creating database mapped getter-callbacks.
     *
     * @param mixed                                         $Property   A reference to the field or value the getter should return.
     * @param string                                        $Type       The return-type of the getter.
     *
     * @param bool                                          $Nullable   Flag indicating whether the referenced property is nullable.
     * @param int|string|null                               $ID         The ID the MappedGetter belongs to.
     * @param \vDesk\DataProvider\Expression\ISelect $Expression The Expression to execute to retrieve the requested property's
     *                                                                  value from the database.
     *
     * @param string                                        $Field      The field of the identifier to use in the where condition of the specified Expression.
     *
     * @param string                                        $FieldType  The type of the field of the identifier to use in the where condition of the specified
     *                                                                  Expression.
     *
     * @return callable A generated getter.
     */
    public static function Create(
        &$Property,
        string $Type,
        bool $Nullable = false,
        &$ID = null,
        ISelect $Expression = null,
        string $Field = "ID",
        string $FieldType = Type::Int
    ): callable {

        // Check if the specified return-type is a scalar non nullable value-type.
        switch($Type) {
            case Type::String:
                return $Nullable
                    ? static function() use (&$Property, &$ID, &$Expression, $Field): ?string {
                        if($Property === null && $ID !== null && $Expression !== null) {
                            $Property = (string)$Expression->Where([$Field => $ID])();
                        }
                        return $Property;
                    }
                    : static function() use (&$Property, &$ID, &$Expression, $Field): string {
                        if($Property === null && $ID !== null && $Expression !== null) {
                            $Property = (string)$Expression->Where([$Field => $ID])();
                        }
                        return $Property;
                    };
            case Type::Int:
                return $Nullable
                    ? static function() use (&$Property, &$ID, &$Expression, $Field): ?int {
                        if($Property === null && $ID !== null && $Expression !== null) {
                            $Property = (int)$Expression->Where([$Field => $ID])();
                        }
                        return $Property;
                    }
                    : static function() use (&$Property, &$ID, &$Expression, $Field): int {
                        if($Property === null && $ID !== null && $Expression !== null) {
                            $Property = (int)$Expression->Where([$Field => $ID])();
                        }
                        return $Property;
                    };
            case Type::Float:
                return $Nullable
                    ? static function() use (&$Property, &$ID, &$Expression, $Field): ?float {
                        if($Property === null && $ID !== null && $Expression !== null) {
                            $Property = (float)$Expression->Where([$Field => $ID])();
                        }
                        return $Property;
                    }
                    : static function() use (&$Property, &$ID, &$Expression, $Field): float {
                        if($Property === null && $ID !== null && $Expression !== null) {
                            $Property = (float)$Expression->Where([$Field => $ID])();
                        }
                        return $Property;
                    };
            case Type::Bool:
                return $Nullable
                    ? static function() use (&$Property, &$ID, &$Expression, $Field): ?bool {
                        if($Property === null && $ID !== null && $Expression !== null) {
                            $Property = (bool)$Expression->Where([$Field => $ID])();
                        }
                        return $Property;
                    }
                    : static function() use (&$Property, &$ID, &$Expression, $Field): bool {
                        if($Property === null && $ID !== null && $Expression !== null) {
                            $Property = (bool)$Expression->Where([$Field => $ID])();
                        }
                        return $Property;
                    };
            case Extension\Type::Time:
                return $Nullable
                    ? static function() use (&$Property, &$ID, &$Expression, $Field): ?\DateTime {
                        if($Property === null && $ID !== null && $Expression !== null) {
                            $Property = \DateTime::createFromFormat("H:i:s", $Expression->Where([$Field => $ID])());
                        }
                        return $Property;
                    }
                    : static function() use (&$Property, &$ID, &$Expression, $Field): \DateTime {
                        if($Property === null && $ID !== null && $Expression !== null) {
                            $Property = \DateTime::createFromFormat("H:i:s", $Expression->Where([$Field => $ID])());
                        }
                        return $Property;
                    };
            case \DateTime::class:
                return $Nullable
                    ? static function() use (&$Property, &$ID, &$Expression, $Field): ?\DateTime {
                        if($Property === null && $ID !== null && $Expression !== null) {
                            $Property = new \DateTime($Expression->Where([$Field => $ID])());
                        }
                        return $Property;
                    }
                    : static function() use (&$Property, &$ID, &$Expression, $Field): \DateTime {
                        if($Property === null && $ID !== null && $Expression !== null) {
                            $Property = new \DateTime($Expression->Where([$Field => $ID])());
                        }
                        return $Property;
                    };
            case Type::Object:
                return $Nullable
                    ? static function() use (&$Property, &$ID, &$Expression, $Field): ?object {
                        if($Property === null && $ID !== null && $Expression !== null) {
                            $Property = \json_decode($Expression->Where([$Field => $ID])());
                        }
                        return $Property;
                    }
                    : static function() use (&$Property, &$ID, &$Expression, $Field): object {
                        if($Property === null && $ID !== null && $Expression !== null) {
                            $Property = \json_decode($Expression->Where([$Field => $ID])());
                        }
                        return $Property;
                    };
            case Type::Array:
                return $Nullable
                    ? static function() use (&$Property, &$ID, &$Expression, $Field): ?array {
                        if($Property === null && $ID !== null && $Expression !== null) {
                            $Property = \json_decode($Expression->Where([$Field => $ID])());
                        }
                        return $Property;
                    }
                    : static function() use (&$Property, &$ID, &$Expression, $Field): array {
                        if($Property === null && $ID !== null && $Expression !== null) {
                            $Property = \json_decode($Expression->Where([$Field => $ID])());
                        }
                        return $Property;
                    };
            case Type::Mixed:
                return $Nullable
                    ? static function() use (&$Property, &$ID, &$Expression, $Field) {
                        if($Property === null && $ID !== null && $Expression !== null) {
                            $Property = \json_decode($Expression->Where([$Field => $ID])());
                        }
                        return $Property;
                    }
                    : static function() use (&$Property, &$ID, &$Expression, $Field) {
                        if($Property === null && $ID !== null && $Expression !== null) {
                            $Property = \json_decode($Expression->Where([$Field => $ID])());
                        }
                        if($Property === null) {
                            throw new \TypeError("Return value of getter cannot be null!");
                        }
                        return $Property;
                    };
        }
        if(\in_array(ICollectionModel::class, \class_implements($Type))) {
            return $Nullable
                ? static function() use (&$Property, &$ID, &$Expression, $Type, $Field, $FieldType): ?ICollectionModel {
                    if($Property === null && $ID !== null && $Expression !== null) {
                        switch($FieldType) {
                            case Type::Int:
                                $Property = new $Type([], (int)$Expression->Where([$Field => $ID])());
                                break;
                            case Type::Float:
                                $Property = new $Type([], (float)$Expression->Where([$Field => $ID])());
                                break;
                            case Type::String:
                                $Property = new $Type([], $Expression->Where([$Field => $ID])());
                                break;

                        }
                    }
                    return $Property;
                }
                : static function() use (&$Property, &$ID, &$Expression, $Type, $Field, $FieldType): ICollectionModel {
                    if($Property === null && $ID !== null && $Expression !== null) {
                        switch($FieldType) {
                            case Type::Int:
                                $Property = new $Type([], (int)$Expression->Where([$Field => $ID])());
                                break;
                            case Type::Float:
                                $Property = new $Type([], (float)$Expression->Where([$Field => $ID])());
                                break;
                            case Type::String:
                                $Property = new $Type([], $Expression->Where([$Field => $ID])());
                                break;

                        }
                    }
                    return $Property;
                };
        }
        return $Nullable
            ? static function() use (&$Property, &$ID, &$Expression, $Type, $Field, $FieldType): ?IManagedModel {
                if($Property === null && $ID !== null && $Expression !== null) {
                    switch($FieldType) {
                        case Type::Int:
                            $Property = new $Type((int)$Expression->Where([$Field => $ID])());
                            break;
                        case Type::Float:
                            $Property = new $Type((float)$Expression->Where([$Field => $ID])());
                            break;
                        case Type::String:
                            $Property = new $Type($Expression->Where([$Field => $ID])());
                            break;

                    }
                }
                return $Property;
            }
            : static function() use (&$Property, &$ID, &$Expression, $Type, $Field, $FieldType): IManagedModel {
                if($Property === null && $ID !== null && $Expression !== null) {
                    switch($FieldType) {
                        case Type::Int:
                            $Property = new $Type((int)$Expression->Where([$Field => $ID])());
                            break;
                        case Type::Float:
                            $Property = new $Type((float)$Expression->Where([$Field => $ID])());
                            break;
                        case Type::String:
                            $Property = new $Type($Expression->Where([$Field => $ID])());
                            break;

                    }
                }
                return $Property;
            };
    }
}