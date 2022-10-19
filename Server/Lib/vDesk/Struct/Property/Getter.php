<?php
declare(strict_types=1);

namespace vDesk\Struct\Property;

use vDesk\Struct\Type;

/**
 * Represents a static factory-class for creating getter-callback-functions to use in properties.
 *
 * @package vDesk
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
abstract class Getter {
    
    /**
     * Factory method for creating getter-callbacks.
     *
     * @param mixed  $Property A reference to the field or value the getter should return.
     * @param string $Type     The return-type of the getter.
     *
     * @param bool   $Nullable Flag indicating whether the referenced property is nullable.
     *
     * @return callable A generated getter.
     */
    public static function Create(mixed &$Property, string $Type = Type::Mixed, bool $Nullable = false): callable {
        switch($Type) {
            case Type::String:
                return $Nullable
                    ? static fn(): ?string => $Property
                    : static fn(): string => $Property;
            case Type::Int:
                return $Nullable
                    ? static fn(): ?int => $Property
                    : static fn(): int => $Property;
            case Type::Float:
                return $Nullable
                    ? static fn(): ?float => $Property
                    : static fn(): float => $Property;
            case Type::Bool:
                return $Nullable
                    ? static fn(): ?bool => $Property
                    : static fn(): bool => $Property;
            case Type::Array:
                return $Nullable
                    ? static fn(): ?array => $Property
                    : static fn(): array => $Property;
            case Type::Iterable:
                return $Nullable
                    ? static fn(): ?iterable => $Property
                    : static fn(): iterable => $Property;
            case Type::Callable:
                return $Nullable
                    ? static fn(): ?callable => $Property
                    : static fn(): callable => $Property;
            case Type::Object:
                return $Nullable
                    ? static fn(): ?object => $Property
                    : static fn(): object => $Property;
            case Type::Mixed:
                return $Nullable
                    ? static fn() => $Property
                    : static function() use (&$Property) {
                        if($Property === null) {
                            throw new \TypeError("Return value of non nullable property cannot be null.");
                        }
                        return $Property;
                    };
        }
        //Default getter.
        if(!\class_exists($Type)) {
            throw new \InvalidArgumentException("Class of type '$Type' doesn't exist!");
        }
        return $Nullable
            ? static function() use (&$Property, $Type): ?object {
                if($Property !== null && !$Property instanceof $Type) {
                    throw Type::Error("Property", $Type, $Property);
                }
                return $Property;
            }
            : static function() use (&$Property, $Type): object {
                if(!$Property instanceof $Type) {
                    throw Type::Error("Property", $Type, $Property);
                }
                return $Property;
            };
    }
    
}