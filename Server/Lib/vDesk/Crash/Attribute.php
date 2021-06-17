<?php
declare(strict_types=1);

namespace vDesk\Crash;

/**
 * Base class for Test and -case attributes.
 *
 * @package vDesk\Crash
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
abstract class Attribute {

    protected array $Arguments = [];

    /**
     * Applies a set of values to the properties of the Attribute.
     *
     * @param array $Values The key-value separated values to apply.
     */
    protected function Apply(array $Values): void {
        $this->Arguments = $Values;
        foreach($Values as $Name => $Value) {
            if(\property_exists(static::class, (string)$Name)) {
                $this->{$Name} = $Value;
            }
        }
    }

    /**
     * Creates a new Attribute from a specified ReflectionAttribute.
     *
     * @param \ReflectionAttribute $Reflector The reflector to create a new Attribute of.
     *
     * @return \vDesk\Crash\Attribute An Attribute that represents the specified Reflector.
     */
    public static function FromReflector(\ReflectionAttribute $Reflector): Attribute {
        /** @var \ReflectionAttribute $DataView */
        $Class = $Reflector->getName();
        if(!\class_exists($Class)) {
            throw new \InvalidArgumentException("\"{$Reflector->getName()}\" is not a valid Attribute class!");
        }
        $Attribute = new $Class();
        $Attribute->Apply($Reflector->getArguments());
        return $Attribute;
    }

    /**
     * Creates a parsable string representation of the Attribute.
     *
     * @return string A parsable string representation of the Attribute.
     */
    public function __toString() {
        $String = "#[\\" . static::class;
        if(\count($this->Arguments) > 0) {
            $String .= "(";
            $Values = [];
            foreach($this->Arguments as $Name => $Value) {
                $Values[] = "{$Name}: " . \json_encode($Value);
            }
            $String .= \implode(", ", $Values) . ")";
        }
        return $String . "]";
    }
}