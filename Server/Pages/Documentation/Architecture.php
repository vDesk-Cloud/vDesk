<?php
declare(strict_types=1);

namespace Pages\Documentation;

use vDesk\Pages\Cached\Page;

/**
 * Architecture Documentation Page.
 *
 * @package vDesk\Documentation
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class Architecture extends Page {

    /** @inheritDoc */
    public function __construct(
        ?iterable $Values = [],
        ?iterable $Templates = ["Documentation/Architecture"],
        ?iterable $Stylesheets = ["Documentation/Topics", "Documentation/Code"],
        ?iterable $Scripts = [],
        public string $Name = "Architecture",
        public string $Label = "Architecture",
        public string $Title = "vDesk - Documentation",
        public string $Description = "Architecture"
    ) {
        parent::__construct($Values, $Templates, $Stylesheets, $Scripts);
    }
}