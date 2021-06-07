<?php
declare(strict_types=1);

namespace Pages\Reflect;

/**
 * Trait Page class.
 *
 * @package Reflect
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class TraitPage extends Documentation {

    /**
     * Initializes a new instance of the Trait Page class.
     *
     * @param null|iterable         $Values      Initializes the Trait Page with the specified Dictionary of values.
     * @param null|iterable         $Templates   Initializes the Trait Page with the specified Collection of templates.
     * @param null|iterable         $Stylesheets Initializes the Trait Page with the specified Collection of stylesheets.
     * @param null|iterable         $Scripts     Initializes the Trait Page with the specified Collection of scripts.
     * @param null|\ReflectionClass $Reflector   Initializes the Trait Page with the specified Reflector.
     */
    public function __construct(
        ?iterable $Values = [],
        ?iterable $Templates = ["Reflect/Trait"],
        ?iterable $Stylesheets = [],
        ?iterable $Scripts = [],
        ?\Reflector $Reflector = null
    ) {
        parent::__construct($Values, $Templates, $Stylesheets, $Scripts, $Reflector);
    }

}