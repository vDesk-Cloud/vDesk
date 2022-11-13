<?php
declare(strict_types=1);

namespace Pages\Documentation;

use Pages\Documentation;

/**
 * Operating systems Documentation Page.
 *
 * @package vDesk\Documentation
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class OperatingSystems extends Documentation {
    
    /**
     * Initializes a new instance of the OperatingSystems Page.
     *
     * @param null|iterable $Values      Initializes the OperatingSystems Page with the specified Dictionary of values.
     * @param null|iterable $Templates   Initializes the OperatingSystems Page with the specified Collection of templates.
     * @param null|iterable $Stylesheets Initializes the OperatingSystems Page with the specified Collection of stylesheets.
     * @param null|iterable $Scripts     Initializes the OperatingSystems Page with the specified Collection of scripts.
     * @param array         $Pages       Initializes the OperatingSystems Page with the specified set of Pages.
     */
    public function __construct(
        ?iterable $Values = [],
        ?iterable $Templates = ["Documentation/OperatingSystems"],
        ?iterable $Stylesheets = [],
        ?iterable $Scripts = [],
        array $Pages = [],
        public string $Name = "OperatingSystems",
        public string $Label = "Operating systems",
        public string $Title = "Operating systems vDesk runs on",
        public string $Description = "Operating systems",
    ) {
        parent::__construct($Values, $Templates, $Stylesheets, $Scripts, $Pages);
    }
    
}