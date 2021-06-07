<?php
declare(strict_types=1);

namespace Pages\Reflect\Method;

use Pages\Reflect;
use Pages\Reflect\Type;

/**
 * Method\Parameter Page class.
 *
 * @package Reflect
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class Parameter extends Reflect {

    /**
     * Initializes a new instance of the Method\Parameter Page class.
     *
     * @param null|iterable   $Values       Initializes the Method\Parameter Page with the specified set of values.
     * @param null|iterable   $Templates    Initializes the Method\Parameter Page with the specified Collection of templates.
     * @param null|iterable   $Stylesheets  Initializes the Method\Parameter Page with the specified Collection of stylesheets.
     * @param null|iterable   $Scripts      Initializes the Method\Parameter Page with the specified Collection of scripts.
     * @param null|\Reflector $Reflector    Initializes the Method\Parameter Page with the specified Reflector.
     * @param null|string     $Modifier     Initializes the Method\Parameter Page with the specified modifier.
     * @param null|Type       $Type         Initializes the Method\Parameter Page with the specified Type.
     * @param null|string     $Name         Initializes the Method\Parameter Page with the specified name.
     * @param bool            $Nullable     Initializes the Method\Parameter Page with the specified flag indicating whether the Method\Parameter is nullable.
     * @param bool            $Optional     Initializes the Method\Parameter Page with the specified flag indicating whether the Method\Parameter is optional.
     * @param bool            $Variadic     Initializes the Method\Parameter Page with the specified flag indicating whether the Method\Parameter is variadic.
     * @param bool            $Reference    Initializes the Method\Parameter Page with the specified flag indicating whether the Method\Parameter is a reference.
     * @param bool            $DefaultValue Initializes the Method\Parameter Page with the specified flag indicating whether the Method\Parameter has a default value.
     * @param null|mixed      $Value        Initializes the Method\Parameter Page with the specified default value.
     * @param string          $Description  Initializes the Method\Parameter Page with the specified description.
     */
    public function __construct(
        ?iterable $Values = [],
        ?iterable $Templates = ["Reflect/Method/Parameter"],
        ?iterable $Stylesheets = [],
        ?iterable $Scripts = [],
        ?\Reflector $Reflector = null,
        public ?string $Modifier = null,
        public ?Type $Type = null,
        public ?string $Name = null,
        public bool $Nullable = false,
        public bool $Optional = false,
        public bool $Variadic = false,
        public bool $Reference = false,
        public bool $DefaultValue = false,
        public mixed $Value = null,
        public string $Description = ""
    ) {
        parent::__construct($Values, $Templates, $Stylesheets, $Scripts, $Reflector);
        $this->Type         ??= new Type(Signature: \gettype($Reflector->getType()));
        $this->Name         ??= $Reflector->name;
        $this->Nullable     ??= $Reflector->allowsNull();
        $this->Optional     ??= $Reflector->isOptional();
        $this->Variadic     ??= $Reflector->isVariadic();
        $this->Reference    ??= $Reflector->isPassedByReference();
        $this->DefaultValue ??= $Reflector->isDefaultValueAvailable();
        if($Reflector->isDefaultValueAvailable()) {
            $this->Value = $Reflector->getDefaultValue();
        }
    }

}
