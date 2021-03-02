<?php
declare(strict_types=1);

namespace Pages\Reflect\Method;


use Pages\Reflect;
use Pages\Reflect\Type;

/**
 * Class Parameter
 *
 * @package Pages\Reflect
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class Parameter extends Reflect {

    /**
     * Initializes a new instance of the Member class.
     *
     * @param null|iterable   $Values      Initializes the Member Page with the specified set of values.
     * @param null|iterable   $Templates   Initializes the Member Page with the specified Collection of templates.
     * @param null|iterable   $Stylesheets Initializes the Member Page with the specified Collection of stylesheets.
     * @param null|iterable   $Scripts     Initializes the Member Page with the specified Collection of scripts.
     * @param null|\Reflector $Reflector   Initializes the Member Page with the specified Reflector.
     * @param null|string     $Modifier    Initializes the Member Page with the specified modifier.
     * @param null|Type       $Type        Initializes the Member Page with the specified Type.
     * @param null|string     $Name        Initializes the Member Page with the specified name.
     * @param bool            $Nullable
     * @param bool            $Optional
     * @param bool            $Variadic
     * @param bool            $Reference
     * @param null|string     $DefaultValue
     * @param null|mixed      $Value
     * @param string          $Description Initializes the Member Page with the specified description.
     *
     * @throws \JsonException
     */
    public function __construct(
        ?iterable $Values = [],
        ?iterable $Templates = ["Reflect/Method/Parameter"],
        ?iterable $Stylesheets = ["Reflect/Stylesheet"],
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
        $this->Name         = $Reflector->name;
        $this->Nullable     = $Reflector->allowsNull();
        $this->Optional     = $Reflector->isOptional();
        $this->Variadic     = $Reflector->isVariadic();
        $this->Reference    = $Reflector->isPassedByReference();
        $this->DefaultValue = $Reflector->isDefaultValueAvailable();
        if($Reflector->isDefaultValueAvailable()) {
            $this->Value = $Reflector->getDefaultValue();
        }

    }

}
