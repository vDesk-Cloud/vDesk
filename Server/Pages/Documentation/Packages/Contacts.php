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
class Contacts extends Page {
    
    /** @inheritDoc */
    public function __construct(
        ?iterable $Values = [],
        ?iterable $Templates = ["Documentation/Packages/Contacts"],
        ?iterable $Stylesheets = [],
        ?iterable $Scripts = [],
        public string $Name = "Contacts",
        public string $Label = "Contacts",
        public string $Title = "Contacts",
        public string $Description = "This document describes how to dispatch and listen on custom events in vDesk"
    ) {
        parent::__construct($Values, $Templates, $Stylesheets, $Scripts);
    }
}