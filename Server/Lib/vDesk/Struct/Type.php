<?php
declare(strict_types=1);

namespace vDesk\Struct;

/**
 * Class Type represents ...
 *
 * @package vDesk\Struct
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 */
abstract class Type {
    
    /**
     * string
     */
    public const String = "string";
    
    /**
     * int
     */
    public const Int = "int";
    
    /**
     * float
     */
    public const Float = "float";
    
    /**
     * bool
     */
    public const Bool = "bool";
    
    /**
     * boolean
     */
    public const Boolean = "boolean";
    
    /**
     * array
     */
    public const Array   = "array";
    
    /**
     * object
     */
    public const Object = "object";
    
    /**
     * mixed
     */
    public const Mixed    = "mixed";
    
    /**
     * callable
     */
    public const Callable = "callable";
    
    /**
     * iterable
     */
    public const Iterable = "iterable";
    
    /**
     * resource
     */
    public const Resource = "resource";
    
    /**
     * null
     */
    public const Null = "null";
    
    /**
     * Enumeration of scalar value types.
     */
    public const Scalar = [
        Type::String,
        Type::Int,
        Type::Float,
        Type::Bool,
        Type::Boolean
    ];
    
    /**
     * Determines whether a specified value is of a scalar or an object-type.
     *
     * @param string $Value The value to check.
     *
     * @return bool True if the specified value is scalar; otherwise, false.
     */
    public static function IsScalar($Value): bool {
        return \is_scalar($Value);
    }
    
    /**
     * Determines whether a specified type is scalar or an object-type.
     *
     * @param string $Type The type to check.
     *
     * @return bool True if the specified type is scalar; otherwise, false.
     */
    public static function IsScalarType(string $Type): bool {
        return \in_array($Type, self::Scalar, false);
    }
    
    /**
     * Determines the type of a specified value.
     *
     * @param $Value mixed The value to determine its type of.
     *
     * @return string The name of the type of the specified value.
     */
    public static function Of($Value): string {
        return (($Type = \gettype($Value)) === static::Object) && !$Value instanceof \stdClass ? \get_class($Value) : \strtolower($Type);
    }
    
    /**
     * Creates an error message for TypeErrors in an uniform format.
     *
     * @param string $Name     The name of the value.
     * @param string $Expected The expected type of the value.
     * @param mixed  $Passed   The passed value.
     *
     * @return string A string containing an error message for TypeErrors in an uniform format.
     */
    public static function ErrorMessage(string $Name, string $Expected, $Passed): string {
        return "{$Name} must be an instance of " . self::Of($Expected) . ", " . self::Of($Passed) . " given.";
    }
    
    /**
     * Convenience-method that creates a TypeError in an uniform format.
     *
     * @param string          $Name     The name of the value.
     * @param string          $Expected The expected type of the value.
     * @param mixed           $Passed   The passed value.
     * @param int             $Code     The code of the TypeError.
     * @param \Throwable|null $Previous The previous occurred Exception.
     *
     * @return \TypeError A TypeError containing a pre-formatted error message.
     */
    public static function Error(string $Name, $Expected, $Passed, int $Code = 0, \Throwable $Previous = null): \TypeError {
        return new \TypeError(self::ErrorMessage($Name, $Expected, $Passed), $Code, $Previous);
    }
    
}