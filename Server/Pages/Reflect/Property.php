<?php
declare(strict_types=1);

namespace Pages\Reflect;

use Pages\Reflect;

/**
 * Property documentation Page.
 *
 * @package Pages\Reflect
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class Property extends Reflect {

    /**
     * Initializes a new instance of the Property class.
     *
     * @param null|iterable            $Values      Initializes the Property Page with the specified set of values.
     * @param null|iterable            $Templates   Initializes the Property Page with the specified Collection of templates.
     * @param null|iterable            $Stylesheets Initializes the Property Page with the specified Collection of stylesheets.
     * @param null|iterable            $Scripts     Initializes the Property Page with the specified Collection of scripts.
     * @param null|\ReflectionProperty $Reflector   Initializes the Property Page with the specified Reflector.
     * @param null|string              $Modifier    Initializes the Property Page with the specified modifier.
     * @param null|Type                $Type        Initializes the Property Page with the specified Type.
     * @param null|string              $Name        Initializes the Property Page with the specified name.
     * @param mixed                    $Value       Initializes the Property Page with the specified value.
     * @param string                   $Description Initializes the Property Page with the specified description.
     * @param bool                     $Static
     */
    public function __construct(
        ?iterable $Values = [],
        ?iterable $Templates = ["Reflect/Property"],
        ?iterable $Stylesheets = ["Reflect/Stylesheet"],
        ?iterable $Scripts = [],
        ?\ReflectionProperty $Reflector = null,
        public ?string $Modifier = null,
        public ?Type $Type = null,
        public ?string $Name = null,
        public $Value = null,
        public string $Description = "",
        public bool $Static = false
    ) {
        parent::__construct($Values, $Templates, $Stylesheets, $Scripts, $Reflector);

        //Get modifier.
        $this->Modifier = \implode(" ", \Reflection::getModifierNames($Reflector->getModifiers()));
        $this->Static   = $Reflector->isStatic();

        //Get value.
        $Reflector->setAccessible(true);
        if($Reflector->isDefault()) {
            $this->Value = $Reflector->getDefaultValue();
        } else if($Reflector->isStatic()) {
            $this->Value = $Reflector->getValue();
        } else {
            $this->Value = null;
        }

        //Get type.
        $TypeMatches = [];
        if((bool)\preg_match(static::Variable, (string)$Reflector->getDocComment(), $TypeMatches)) {
            $this->Type = new Type(Signature: (string)$Reflector->getType());
        } else {
            $this->Type = new Type(Signature: \gettype($this->Value));
        }

        //Get name.
        $this->Name = $Reflector->name;

        //Get description.
        $Matches = [];
        if((bool)\preg_match(static::Description, (string)$Reflector->getDocComment(), $Matches)) {
            $this->Description = \trim($Matches[0], "* \n\r");
        }

    }

}