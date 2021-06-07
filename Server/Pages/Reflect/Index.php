<?php
declare(strict_types=1);

namespace Pages\Reflect;

use vDesk\Pages\Page;

/**
 * Index Page class.
 *
 * @package Reflect
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class Index extends Page {

    /**
     * Initializes a new instance of the Index Page.
     *
     * @param null|iterable         $Values      Initializes the Index Page with the specified Dictionary of values.
     * @param null|iterable         $Templates   Initializes the Index Page with the specified Collection of templates.
     * @param null|iterable         $Stylesheets Initializes the Index Page with the specified Collection of stylesheets.
     * @param null|iterable         $Scripts     Initializes the Index Page with the specified Collection of scripts.
     * @param \ReflectionClass[]    $Reflectors  Initializes the Index Page with the specified set of ReflectionClasses.
     * @param null|\ReflectionClass $Current     Initializes the Index Page with the specified current ReflectionClass.
     * @param \ReflectionClass[]    $Classes     Initializes the Index Page with the specified set of class-ReflectionClasses.
     * @param \ReflectionClass[]    $Interfaces  Initializes the Index Page with the specified set of interface-ReflectionClasses.
     * @param \ReflectionClass[]    $Traits      Initializes the Index Page with the specified set of trait-ReflectionClasses.
     */
    public function __construct(
        ?iterable $Values = [],
        ?iterable $Templates = ["Reflect/Index"],
        ?iterable $Stylesheets = [],
        ?iterable $Scripts = [],
        public array $Reflectors = [],
        public ?\ReflectionClass $Current = null,
        public array $Classes = [],
        public array $Interfaces = [],
        public array $Traits = []
    ) {
        parent::__construct($Values, $Templates, $Stylesheets, $Scripts);
        foreach($Reflectors as $Reflector) {
            if($Reflector->isInterface()) {
                $this->Interfaces[] = $Reflector;
            } else if($Reflector->isTrait()) {
                $this->Traits[] = $Reflector;
            } else {
                $this->Classes[] = $Reflector;
            }
            $Reflector->getNamespaceName();
        }
    }

}