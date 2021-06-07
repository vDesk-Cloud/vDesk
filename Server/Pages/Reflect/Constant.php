<?php
declare(strict_types=1);

namespace Pages\Reflect;

use Pages\Reflect;

/**
 * Constant Page class.
 *
 * @package Reflect
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class Constant extends Reflect {

    /**
     * Initializes a new instance of the Constant Page class.
     *
     * @param null|iterable                 $Values      Initializes the Constant Page with the specified set of values.
     * @param null|iterable                 $Templates   Initializes the Constant Page with the specified Collection of templates.
     * @param null|iterable                 $Stylesheets Initializes the Constant Page with the specified Collection of stylesheets.
     * @param null|iterable                 $Scripts     Initializes the Constant Page with the specified Collection of scripts.
     * @param null|\ReflectionClassConstant $Reflector   Initializes the Constant Page with the specified Reflector.
     * @param null|string                   $Modifier    Initializes the Constant Page with the specified modifier.
     * @param null|Type                     $Type        Initializes the Constant Page with the specified Type.
     * @param null|string                   $Name        Initializes the Constant Page with the specified name.
     * @param mixed                         $Value       Initializes the Constant Page with the specified value.
     * @param string                        $Description Initializes the Constant Page with the specified description.
     */
    public function __construct(
        ?iterable $Values = [],
        ?iterable $Templates = ["Reflect/Constant"],
        ?iterable $Stylesheets = [],
        ?iterable $Scripts = [],
        ?\ReflectionClassConstant $Reflector = null,
        public ?string $Modifier = null,
        public ?Type $Type = null,
        public ?string $Name = null,
        public $Value = null,
        public string $Description = ""
    ) {
        parent::__construct($Values, $Templates, $Stylesheets, $Scripts, $Reflector);

        //Get modifier.
        $this->Modifier = \implode(" ", \Reflection::getModifierNames($Reflector->getModifiers()));

        //Get type.
        $this->Type ??= new Type(Signature: \gettype($Reflector->getValue()));

        //Get name.
        $this->Name = $Reflector->name;

        //Get value.
        $this->Value = $Reflector->getValue();

        //Get description.
        $Matches = [];
        if((bool)\preg_match(static::Description, (string)$Reflector->getDocComment(), $Matches)) {
            $this->Description = \trim(\str_replace(" * ", "", $Matches[0]));
        }

    }

}
