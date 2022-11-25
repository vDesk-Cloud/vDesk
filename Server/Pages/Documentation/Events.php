<?php
declare(strict_types=1);

namespace Pages\Documentation;

use vDesk\Pages\Cached\Page;

/**
 * Custom Releases Documentation Page.
 *
 * @package vDesk\Documentation
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class Events extends Page {
    
    /** @inheritDoc */
    public function __construct(
        ?iterable $Values = [],
        ?iterable $Templates = ["Documentation/Events"],
        ?iterable $Stylesheets = [],
        ?iterable $Scripts = [],
        public string $Name = "Events",
        public string $Label = "Packages",
        public string $Title = "Packages",
        public string $Description = "Events"
    ) {
        parent::__construct($Values, $Templates, $Stylesheets, $Scripts);
    }
}