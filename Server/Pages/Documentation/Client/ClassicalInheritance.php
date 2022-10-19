<?php
declare(strict_types=1);

namespace Pages\Documentation\Client;

use vDesk\Pages\Cached\Page;

/**
 * Classical inheritance Documentation Page.
 *
 * @package vDesk\Documentation
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class ClassicalInheritance extends Page {
    
    /**
     * The name of the Page.
     *
     * @var string
     */
    public string $Name = "ClassicalInheritance";
    
    /**
     * The nav label of the Page
     *
     * @var string
     */
    public string $Description = "Classical inheritance and Interfaces";
    
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
        ?iterable $Templates = ["Documentation/Client/ClassicalInheritance"],
        ?iterable $Stylesheets = [],
        ?iterable $Scripts = [],
    ) {
        parent::__construct($Values, $Templates, $Stylesheets, $Scripts);
    }
    
}