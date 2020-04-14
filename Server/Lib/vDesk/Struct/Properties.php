<?php
declare(strict_types=1);

namespace vDesk\Struct;

/**
 * Constant for getters.
 *
 * @var string
 */
\define("Get", "Get");

/**
 * Constant for setters.
 *
 * @var string
 */
\define("Set", "Set");

/**
 * Trait that enables the definition of properties.
 *
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
trait Properties {

    /**
     * The properties of the Properties-trait.
     *
     * @var \vDesk\Struct\Property[]
     */
    protected array $Properties = [];

    /**
     * Calls the setter of a Property passing the specified value.
     *
     * @param string $Name  The name of the property whose setter is being called.
     * @param mixed  $Value The value to pass to the setter of the specified Property.
     *
     * @throws \vDesk\Struct\AccessViolationException Thrown if the requested property has no setter.
     * @throws \vDesk\Struct\InvalidOperationException Thrown if a non existent property is being accessed.
     */
    public final function __set(string $Name, $Value): void {
        // Check if the property exists.
        if(!isset($this->Properties[$Name])) {
            throw new InvalidOperationException("Undefined property: " . static::class . "::$Name.");
        }
        // Check if the property has a setter.
        if(!\is_callable($this->Properties[$Name]->Setter)) {
            throw new AccessViolationException("Cannot access private property " . static::class . "::$Name.");
        }
        ($this->Properties[$Name]->Setter)($Value);
    }

    /**
     * Calls the getter of a Property.
     *
     * @param string $Name The name of the property whose getter is being called.
     *
     * @throws \vDesk\Struct\AccessViolationException Thrown if the requested property has no getter.
     * @throws \vDesk\Struct\InvalidOperationException Thrown if a non existent property is being accessed.
     *
     * @return mixed The value of the property's getter.
     */
    public final function __get(string $Name) {
        // Check if the property exists.
        if(isset($this->Properties[$Name])) {
            // Check if the property has a getter.
            if(\is_callable($this->Properties[$Name]->Getter)) {
                return ($this->Properties[$Name]->Getter)();
            }
        }
    }

    /**
     * Adds a new {@link \vDesk\Struct\Property} property to the object.
     *
     * <code>
     * //Example of defining properties
     * class Foo {
     *
     *      private string $PrivateMember = "I'm private!";
     *
     *      public function __construct(){
     *          $this->AddProperty("PrivateValue")
     *               ->Get(fn(): string => $this->PrivateMember)
     *               ->Set(fn(string $Value) => $this->PrivateMember = $Value);
     *      }
     * }
     *
     * $oFoo = new Foo();
     * //Calling getter.
     * echo $oFoo->PrivateValue;
     * // prints: 'I'm private!'
     *
     * //Calling setter.
     * $oFoo->PrivateValue = "I'm not private anymore!";
     * echo $oFoo->PrivateValue;
     * // prints: 'I'm not private anymore!'
     * </code>
     *
     * @param string     $Name      The name of the property by which its been accessible.
     * @param array|null $Accessors Adds a property with the specified getter and/or setter.
     *
     * @return \vDesk\Struct\Property Returns a new {@link \vDesk\Struct\Property} with the given name.
     */
    protected final function AddProperty(string $Name, array $Accessors = null): Property {
        return $this->Properties[$Name] = new Property($Accessors);
    }

    /**
     * Adds a set of {@link \vDesk\Struct\Property} properties to the object.
     *
     * <code>
     * //Example of defining properties
     * class Foo{
     *
     *     private string $PrivateMember = "I'm private!";
     *
     *     public function __construct(){
     *          $this->AddProperties([
     *                "PrivateValue" => [
     *                    Get => fn(): string => $this->PrivateMember,
     *                    Set => fn(string $Value) => $this->PrivateMember = $Value
     *                ],
     *                "FurtherProperty" => [
     *                    Get => function () {
     *                    },
     *                    Set => function () {
     *                    }
     *                ]
     *           ]);
     *      }
     * }
     *
     * $Foo = new Foo();
     * //Calling getter.
     * echo $Foo->PrivateValue;
     * // prints: 'I'm private!'
     *
     * //Calling setter.
     * $Foo->PrivateValue = "I'm not private anymore!";
     * echo $Foo->PrivateValue;
     * // prints: 'I'm not private anymore!'
     * </code>
     *
     * @param array $Properties An array of property-descriptors to define on the class this method is called upon.
     *
     * @return self The instance itself for further chaining.
     */
    protected final function AddProperties(array $Properties): self {
        foreach($Properties as $Name => $Accessors) {
            $this->Properties[$Name] = new Property($Accessors);
        }
        return $this;
    }
}