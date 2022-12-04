<?php
declare(strict_types=1);

namespace Pages\Documentation\Packages;

use vDesk\Pages\Cached\Page;

/**
 * DataProvider Documentation Page.
 *
 * @package vDesk\Documentation
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class DataProvider extends Page {

    /** @inheritDoc */
    public function __construct(
        ?iterable $Values = [],
        ?iterable $Templates = ["Documentation/Packages/DataProvider"],
        ?iterable $Stylesheets = [],
        ?iterable $Scripts = [],
        public string $Name = "DataProvider",
        public string $Label = "DataProvider",
        public string $Title = "Database access in vDesk",
        public string $Description = "DataProvider access"
    ) {
        parent::__construct($Values, $Templates, $Stylesheets, $Scripts);
    }
}