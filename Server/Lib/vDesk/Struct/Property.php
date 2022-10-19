<?php
declare(strict_types=1);

namespace vDesk\Struct;

/**
 * Represents a property providing get and set-methods.
 *
 * @package vDesk
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class Property {
    
    /**
     * Gets or sets the Setter of the Property.
     *
     * @var null|callable
     */
    public $Getter = null;
    
    /**
     * Gets or sets the Getter of the Property.
     *
     * @var null|callable
     */
    public $Setter = null;

    /**
     * Initializes a new instance of the Property class.
     *
     * @param null|callable $Getter Initializes the Property with the specified getter callback.
     * @param null|callable $Setter Initializes the Property with the specified setter callback.
     */
    public function __construct(?callable $Getter = null, ?callable $Setter = null) {
        $this->Getter = $Getter;
        $this->Setter = $Setter;
    }
    
    /**
     * Adds a getter function to the Property.
     *
     * @param callable $Getter The function to call on accessing the Property's value.
     *
     * @return \vDesk\Struct\Property The instance of the Property for further chaining.
     */
    public function Get(callable $Getter): Property {
        $this->Getter = $Getter;
        return $this;
    }
    
    /**
     * Adds a setter function to the Property.
     *
     * @param callable $Setter The function to call on setting the Property's value.
     *
     * @return \vDesk\Struct\Property The instance of the Property for further chaining.
     */
    public function Set(callable $Setter): Property {
        $this->Setter = $Setter;
        return $this;
    }
    
    /**
     * Calls the getter or setter of the Property depending if a value has been passed if the Property is being invoked.
     *
     * @param null $Value
     *
     * @return mixed|null The return value of the getter of the Property; otherwise, null.
     */
    public function __invoke($Value = null) {
        if($Value === null) {
            return $this->Getter();
        }
        $this->Setter($Value);
        return null;
    }
}