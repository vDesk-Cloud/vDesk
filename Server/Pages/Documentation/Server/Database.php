<?php
declare(strict_types=1);

namespace Pages\Documentation\Server;

use vDesk\Pages\Cached\Page;

/**
 * Database access Documentation Page.
 *
 * @package vDesk\Documentation
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class Database extends Page {

    /** @inheritDoc */
    public function __construct(
        ?iterable $Values = [],
        ?iterable $Templates = ["Documentation/Server/Database"],
        ?iterable $Stylesheets = [],
        ?iterable $Scripts = [],
        public string $Name = "Database",
        public string $Label = "Database access",
        public string $Title = "Database access in vDesk",
        public string $Description = "Database access"
    ) {
        parent::__construct($Values, $Templates, $Stylesheets, $Scripts);
    }
}