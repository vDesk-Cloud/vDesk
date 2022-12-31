<?php
declare(strict_types=1);

namespace Pages\Documentation\Packages;

use vDesk\Pages\Page;

/**
 * Configuration Documentation Page.
 *
 * @package vDesk\Documentation
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class Configuration extends Page {
    
    /** @inheritDoc */
    public function __construct(
        ?iterable $Values = [],
        ?iterable $Templates = ["Documentation/Packages/Configuration"],
        ?iterable $Stylesheets = [],
        ?iterable $Scripts = [],
        public string $Name = "Configuration",
        public string $Label = "Configuration",
        public string $Title = "Configuration",
        public string $Description = "This document describes how to dispatch and listen on custom events in vDesk"
    ) {
        parent::__construct($Values, $Templates, $Stylesheets, $Scripts);
    }
}