<?php
declare(strict_types=1);

namespace Pages\Documentation\Packages;

use vDesk\Pages\Cached\Page;

/**
 * Machines Documentation Page.
 *
 * @package vDesk\Documentation
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class Machines extends Page {

    /** @inheritDoc */
    public function __construct(
        ?iterable $Values = [],
        ?iterable $Templates = ["Documentation/Packages/Machines"],
        ?iterable $Stylesheets = [],
        ?iterable $Scripts = [],
        public string $Name = "Machines",
        public string $Label = "Machines",
        public string $Title = "Machines documentation",
        public string $Description = "Machines"
    ) {
        parent::__construct($Values, $Templates, $Stylesheets, $Scripts);
    }
}