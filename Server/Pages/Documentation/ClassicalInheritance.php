<?php
declare(strict_types=1);

namespace Pages\Documentation;

use vDesk\Pages\Cached\Page;

/**
 * Classical inheritance Documentation Page.
 *
 * @package vDesk\Documentation
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class ClassicalInheritance extends Page {

    /**
     * Initializes a new instance of the ClassicalInheritance Tutorial.
     *
     * @param null|iterable $Values      Initializes the ClassicalInheritance Topic with the specified Dictionary of values.
     * @param null|iterable $Templates   Initializes the ClassicalInheritance Topic with the specified Collection of templates.
     * @param null|iterable $Stylesheets Initializes the ClassicalInheritance Topic with the specified Collection of stylesheets.
     * @param null|iterable $Scripts     Initializes the ClassicalInheritance Topic with the specified Collection of scripts.
     */
    public function __construct(
        ?iterable $Values = [],
        ?iterable $Templates = ["Documentation/ClassicalInheritance"],
        ?iterable $Stylesheets = [],
        ?iterable $Scripts = [],
        public string $Name = "ClassicalInheritance",
        public string $Label = "Classical inheritance and interfaces",
        public string $Title = "Classical inheritance and custom interfaces in JavaScript",
        public string $Description = "Classical inheritance and interfaces"
    ) {
        parent::__construct($Values, $Templates, $Stylesheets, $Scripts);
    }
    
}