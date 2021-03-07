<?php
declare(strict_types=1);

namespace Pages\Reflect;

/**
 * Interface Page class.
 *
 * @package Pages\Reflect\Interface
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class InterfacePage extends Documentation {

    /**
     * Initializes a new instance of the Interface Page class.
     *
     * @param null|iterable         $Values      Initializes the Interface Page with the specified Dictionary of values.
     * @param null|iterable         $Templates   Initializes the Interface Page with the specified Collection of templates.
     * @param null|iterable         $Stylesheets Initializes the Interface Page with the specified Collection of stylesheets.
     * @param null|iterable         $Scripts     Initializes the Interface Page with the specified Collection of scripts.
     * @param null|\ReflectionClass $Reflector   Initializes the Interface Page with the specified Reflector.
     */
    public function __construct(
        ?iterable $Values = [],
        ?iterable $Templates = ["Reflect/Interface"],
        ?iterable $Stylesheets = ["Reflect/Stylesheet"],
        ?iterable $Scripts = [],
        ?\Reflector $Reflector = null
    ) {
        parent::__construct($Values, $Templates, $Stylesheets, $Scripts, $Reflector);
    }

}