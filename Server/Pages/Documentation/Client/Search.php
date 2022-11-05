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
     * The name of the Page.
     *
     * @var string
     */
    public string $Name = "Search";
    
    /**
     * The nav label of the Page.
     *
     * @var string
     */
    public string $Description = "Search filters and -results";
    
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
        ?iterable $Scripts = []
    ) {
        parent::__construct($Values, $Templates, $Stylesheets, $Scripts);
    }
    
}