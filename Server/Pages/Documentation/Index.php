<?php
declare(strict_types=1);

namespace Pages\Documentation;

use Pages\Documentation;

/**
 * Documentation Index Page.
 *
 * @package vDesk\Documentation
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class Index extends Documentation {

    /**
     * Initializes a new instance of the Index Page.
     *
     * @param null|iterable       $Values      Initializes the Index Page with the specified Dictionary of values.
     * @param null|iterable       $Templates   Initializes the Index Page with the specified Collection of templates.
     * @param null|iterable       $Stylesheets Initializes the Index Page with the specified Collection of stylesheets.
     * @param null|iterable       $Scripts     Initializes the Index Page with the specified Collection of scripts.
     * @param \vDesk\Pages\Page[] $Pages       Initializes the Index Page with the specified set of Pages.
     * @param \vDesk\Pages\Page[] $Client      Initializes the Index Page with the specified set of Client Documentation Pages.
     * @param \vDesk\Pages\Page[] $Server      Initializes the Index Page with the specified set of Server Documentation Pages.
     */
    public function __construct(
        ?iterable    $Values = [],
        ?iterable    $Templates = ["Documentation/Index"],
        ?iterable    $Stylesheets = [],
        ?iterable    $Scripts = [],
        array        $Pages = [],
        public array $Client = [],
        public array $Server = [],
        public string $Name = "Index",
        public string $Label = "Operating systems",
        public string $Title = "vDesk - Documentation",
        public string $Description = "Index"
    ) {
        parent::__construct($Values, $Templates, $Stylesheets, $Scripts, $Pages);
    }
}