<?php
declare(strict_types=1);

namespace Pages\Documentation\Client;

use vDesk\Pages\Cached\Page;

/**
 * Search filters and -results Documentation Page.
 *
 * @package vDesk\Documentation
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class Search extends Page {
    

    /**
     * Initializes a new instance of the Search Page.
     *
     * @param null|iterable $Values      Initializes the Search Page with the specified Dictionary of values.
     * @param null|iterable $Templates   Initializes the Search Page with the specified Collection of templates.
     * @param null|iterable $Stylesheets Initializes the Search Page with the specified Collection of stylesheets.
     * @param null|iterable $Scripts     Initializes the Search Page with the specified Collection of scripts.
     */
    public function __construct(
        ?iterable $Values = [],
        ?iterable $Templates = ["Documentation/Client/Search"],
        ?iterable $Stylesheets = [],
        ?iterable $Scripts = [],
        public string $Name = "Search",
        public string $Label = "Search filters and -results",
        public string $Title = "Search filters and -results",
        public string $Description = "Search filters and -results"
    ) {
        parent::__construct($Values, $Templates, $Stylesheets, $Scripts);
    }
    
}