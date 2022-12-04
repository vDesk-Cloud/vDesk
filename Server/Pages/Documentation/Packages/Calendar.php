<?php
declare(strict_types=1);

namespace Pages\Documentation\Packages;

use vDesk\Pages\Cached\Page;

/**
 * Custom Releases Documentation Page.
 *
 * @package vDesk\Documentation
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class Calendar extends Page {
    
    /** @inheritDoc */
    public function __construct(
        ?iterable $Values = [],
        ?iterable $Templates = ["Documentation/Packages/Calendar"],
        ?iterable $Stylesheets = [],
        ?iterable $Scripts = [],
        public string $Name = "Calendar",
        public string $Label = "Calendar",
        public string $Title = "Calendar",
        public string $Description = "This document describes how to dispatch and listen on custom events in vDesk"
    ) {
        parent::__construct($Values, $Templates, $Stylesheets, $Scripts);
    }
}