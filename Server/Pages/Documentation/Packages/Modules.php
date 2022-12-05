<?php
declare(strict_types=1);

namespace Pages\Documentation\Packages;

use vDesk\Pages\Page;

/**
 * Custom Releases Documentation Page.
 *
 * @package vDesk\Documentation
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class Modules extends Page {
    
    /** @inheritDoc */
    public function __construct(
        ?iterable $Values = [],
        ?iterable $Templates = ["Documentation/Packages/Modules"],
        ?iterable $Stylesheets = [],
        ?iterable $Scripts = [],
        public string $Name = "Modules",
        public string $Label = "Modules",
        public string $Title = "Modules",
        public string $Description = "This document describes how to dispatch and listen on custom events in vDesk"
    ) {
        parent::__construct($Values, $Templates, $Stylesheets, $Scripts);
    }
}