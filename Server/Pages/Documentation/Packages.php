<?php
declare(strict_types=1);

namespace Pages\Documentation;

use vDesk\Pages\Page;

/**
 * Packages Documentation
 *
 * @package vDesk\Documentation
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class Packages extends Page {
    
    /**
     * Initializes a new instance of the Packages Page.
     *
     * @param null|iterable $Values      Initializes the Packages Page with the specified Dictionary of values.
     * @param null|iterable $Templates   Initializes the Packages Page with the specified Collection of templates.
     * @param null|iterable $Stylesheets Initializes the Packages Page with the specified Collection of stylesheets.
     * @param null|iterable $Scripts     Initializes the Packages Page with the specified Collection of scripts.
     */
    public function __construct(
        ?iterable $Values = [],
        ?iterable $Templates = ["Documentation/Packages"],
        ?iterable $Stylesheets = [],
        ?iterable $Scripts = [],
        public string $Name = "Packages",
        public string $Label = "Packages",
        public string $Title = "Packages",
        public string $Description = "Packages, updates and setups",
    ) {
        parent::__construct($Values, $Templates, $Stylesheets, $Scripts);
    }
    
}