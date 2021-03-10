<?php
declare(strict_types=1);

namespace Pages\Reflect\Method;

use Pages\Reflect;

/**
 * Method\Exception Page class.
 *
 * @package Reflect
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class Exception extends Reflect {

    /**
     * Initializes a new instance of the Method\Exception Page class.
     *
     * @param null|iterable         $Values      Initializes the Method\Exception Page with the specified Dictionary of values.
     * @param null|iterable         $Templates   Initializes the Method\Exception Page with the specified Collection of templates.
     * @param null|iterable         $Stylesheets Initializes the Method\Exception Page with the specified Collection of stylesheets.
     * @param null|iterable         $Scripts     Initializes the Method\Exception Page with the specified Collection of scripts.
     * @param null|\ReflectionClass $Reflector   Initializes the Method\Exception Page with the specified Reflector.
     * @param null|string           $Name        Initializes the Method\Exception Page with the specified name.
     * @param string                $Description Initializes the Method\Exception Page with the specified description.
     */
    public function __construct(
        ?iterable $Values = [],
        ?iterable $Templates = ["Reflect/Method/Exception"],
        ?iterable $Stylesheets = [],
        ?iterable $Scripts = [],
        ?\ReflectionClass $Reflector = null,
        public ?string $Name = null,
        public string $Description = ""
    ) {
        parent::__construct($Values, $Templates, $Stylesheets, $Scripts, $Reflector);
    }

}