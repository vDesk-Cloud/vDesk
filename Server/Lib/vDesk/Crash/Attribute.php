<?php
declare(strict_types=1);

namespace vDesk\Crash;

use vDesk\Data\IDataView;

/**
 * Base class for Test and -case attributes.
 *
 * @package vDesk\Crash
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
abstract class Attribute implements IDataView {

    /**
     * Initializes a new instance of the Attribute class.
     *
     * @param array $Arguments Initializes the Attribute with the specified set of arguments.
     */
    public function __construct(public array $Arguments = []) {
        foreach($Arguments as $Name => $Value){
            if(\property_exists(static::class, $Name)){
                $this->{$Name} = $Value;
            }
        }
    }

    /**
     * Creates a new Attribute from a specified ReflectionAttribute.
     *
     * @param mixed|\ReflectionAttribute $DataView
     *
     * @return \vDesk\Data\IDataView
     */
    public static function FromDataView(mixed $DataView): IDataView {
        if(!$DataView instanceof \ReflectionAttribute) {
            throw new \InvalidArgumentException("DataView must be an instance of \"\\ReflectionAttribute\"!");
        }
        /** @var \ReflectionAttribute $DataView */
        $Class = $DataView->getName();
        if(!\class_exists($Class)) {
            throw new \InvalidArgumentException("\"{$DataView->getName()}\" is not a valid Attribute class!");
        }
        return new $Class($DataView->getArguments());
    }

    /**
     * Creates a parsable string representation of the Attribute.
     *
     * @return string A parsable string representation of the Attribute.
     */
    public function ToDataView(bool $Reference = false): string {
        $String    = "#[" . ($Reference ? \substr(static::class, \strrpos(static::class, "\\") + 1) : "\\" . static::class);
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

    /**
     * Creates a parsable string representation of the Attribute.
     *
     * @return string A parsable string representation of the Attribute.
     */
    public function __toString() {
        return $this->ToDataView();
    }
}