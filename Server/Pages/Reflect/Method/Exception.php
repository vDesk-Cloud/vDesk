<?php
declare(strict_types=1);

namespace Pages\Reflect\Method;


use Pages\Reflect;

class Exception extends Reflect {

    /**
     *
     * @param null|iterable         $Values
     * @param null|iterable         $Templates
     * @param null|iterable         $Stylesheets
     * @param null|iterable         $Scripts
     * @param null|\ReflectionClass $Reflector
     * @param null|string           $Name
     * @param string                $Description
     */
    public function __construct(
        ?iterable $Values = [],
        ?iterable $Templates = ["Reflect/Method/Exception"],
        ?iterable $Stylesheets = ["Reflect/Stylesheet"],
        ?iterable $Scripts = [],
        ?\ReflectionClass $Reflector = null,
        public ?string $Name = null,
        public string $Description = ""
    ) {
        parent::__construct($Values, $Templates, $Stylesheets, $Scripts, $Reflector);
    }

}