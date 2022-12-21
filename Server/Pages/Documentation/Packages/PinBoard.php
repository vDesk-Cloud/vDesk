<?php
declare(strict_types=1);

namespace Pages\Documentation\Packages;

use vDesk\Pages\Cached\Page;

/**
 * PinBoard Documentation Page.
 *
 * @package vDesk\Documentation
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class PinBoard extends Page {
    
    /** @inheritDoc */
    public function __construct(
        ?iterable $Values = [],
        ?iterable $Templates = ["Documentation/Packages/PinBoard"],
        ?iterable $Stylesheets = [],
        ?iterable $Scripts = [],
        public string $Name = "PinBoard",
        public string $Label = "PinBoard",
        public string $Title = "PinBoard",
        public string $Description = "This document describes how to dispatch and listen on custom events in vDesk"
    ) {
        parent::__construct($Values, $Templates, $Stylesheets, $Scripts);
    }
}