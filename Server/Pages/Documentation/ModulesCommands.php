<?php
declare(strict_types=1);

namespace Pages\Documentation;

use vDesk\Pages\Cached\Page;

/**
 * Class ClassicalInheritance
 *
 * @package Pages\Documentation
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class ModulesCommands extends Page {
    
    /**
     * The name of the Tutorial.
     *
     * @var string
     */
    public string $Name = "ModulesCommands";
    
    /**
     * The nav label of the Tutorial
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