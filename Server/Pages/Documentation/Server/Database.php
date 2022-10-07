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

    /**
     * The name of the Topic.
     *
     * @var string
     */
    public string $Name = "Database";

    /**
     * The nav label of the Topic.
     *
     * @var string
     */
    public string $Description = "Database access";

    /** @inheritDoc */
    public function __construct(
        ?iterable $Values = [],
        ?iterable $Templates = ["Documentation/Server/Database"],
        ?iterable $Stylesheets = [],
        ?iterable $Scripts = [],
    ) {
        parent::__construct($Values, $Templates, $Stylesheets, $Scripts);
    }
}