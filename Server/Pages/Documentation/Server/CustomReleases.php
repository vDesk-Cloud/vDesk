<?php
declare(strict_types=1);

namespace Pages\Documentation\Server;

use vDesk\Pages\Cached\Page;

/**
 * Custom Releases Documentation Page.
 *
 * @package vDesk\Documentation
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class CustomReleases extends Page {
    
    /** @inheritDoc */
    public function __construct(
        ?iterable $Values = [],
        ?iterable $Templates = ["Documentation/Server/CustomReleases"],
        ?iterable $Stylesheets = [],
        ?iterable $Scripts = [],
        public string $Name = "CustomReleases",
        public string $Label = "Custom releases and packages",
        public string $Title = "Custom releases and packagesCustom releases and packages",
        public string $Description = "Custom releases and packages"
    ) {
        parent::__construct($Values, $Templates, $Stylesheets, $Scripts);
    }
}