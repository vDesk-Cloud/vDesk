<?php
declare(strict_types=1);

namespace Pages\Reflect;

/**
 * Class Page class.
 *
 * @package Reflect
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class ClassPage extends Documentation {

    /**
     * Initializes a new instance of the Class Page.
     *
     * @param null|iterable   $Values      Initializes the Class Page with the specified Dictionary of values.
     * @param null|iterable   $Templates   Initializes the Class Page with the specified Collection of templates.
     * @param null|iterable   $Stylesheets Initializes the Class Page with the specified Collection of stylesheets.
     * @param null|iterable   $Scripts     Initializes the Class Page with the specified Collection of scripts.
     * @param null|\Reflector $Reflector   Initializes the Class Page with the specified ReflectionClass.
     * @param bool            $Final       Initializes the Class Page with the specified flag indicating whether the Reflector is final.
     * @param bool            $Abstract    Initializes the Class Page with the specified flag indicating whether the Reflector is abstract.
     */
    public function __construct(
        ?iterable $Values = [],
        ?iterable $Templates = ["Reflect/Class"],
        ?iterable $Stylesheets = ["Reflect/Stylesheet"],
        ?iterable $Scripts = [],
        ?\Reflector $Reflector = null,
        public bool $Final = false,
        public bool $Abstract = false
    ) {
        parent::__construct($Values, $Templates, $Stylesheets, $Scripts, $Reflector);
        $this->Final    = $Reflector->isFinal();
        $this->Abstract = $Reflector->isAbstract();
    }

}