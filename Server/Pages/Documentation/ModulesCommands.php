<?php
declare(strict_types=1);

namespace Pages\Documentation;

use vDesk\Pages\Cached\Page;

/**
 * Modules&Commands Documentation Page.
 *
 * @package vDesk\Documentation
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class ModulesCommands extends Page {
    
    /**
     * The name of the Page.
     *
     * @var string
     */
    public string $Name = "ModulesCommands";
    
    /**
     * The nav label of the Page.
     *
     * @var string
     */
    public string $Description = "Modules & Commands";

    /** @inheritDoc */
    public function __construct(
        ?iterable $Values = [],
        ?iterable $Templates = ["Documentation/ModulesCommands"],
        ?iterable $Stylesheets = [],
        ?iterable $Scripts = []
    ) {
        parent::__construct($Values, $Templates, $Stylesheets, $Scripts);
    }
}