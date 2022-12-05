<?php
declare(strict_types=1);

namespace Pages\Documentation\Packages;

use vDesk\Pages\Page;

/**
 * Updates Package Documentation
 *
 * @package vDesk\Documentation
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class Updates extends Page {
    
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
        ?iterable $Templates = ["Documentation/Packages/Updates"],
        ?iterable $Stylesheets = [],
        ?iterable $Scripts = [],
        public string $Name = "Updates",
        public string $Label = "Updates",
        public string $Title = "Updates package documentation",
        public string $Description = <<<Description
This document explains the update system of vDesk.
It contains technical specifications on installable updates and instructions on how to check for and install updates as well as querying custom update servers.
Description,
        public array $Tags = [""]
    ) {
        parent::__construct($Values, $Templates, $Stylesheets, $Scripts);
    }
    
}