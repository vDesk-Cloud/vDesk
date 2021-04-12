<?php
declare(strict_types=1);

namespace Pages\Documentation\Tutorials;

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
    
    /**
     * Initializes a new instance of the ModulesCommands Tutorial.
     *
     * @param null|iterable $Values      Initializes the ModulesCommands Tutorial with the specified Dictionary of values.
     * @param null|iterable $Templates   Initializes the ModulesCommands Tutorial with the specified Collection of templates.
     * @param null|iterable $Stylesheets Initializes the ModulesCommands Tutorial with the specified Collection of stylesheets.
     * @param null|iterable $Scripts     Initializes the ModulesCommands Tutorial with the specified Collection of scripts.
     */
    public function __construct(
        ?iterable $Values = [],
        ?iterable $Templates = ["Documentation/Tutorials/ModulesCommands"],
        ?iterable $Stylesheets = ["Documentation/Stylesheet", "vDesk/Packages"],
        ?iterable $Scripts = []
    ) {
        parent::__construct($Values, $Templates, $Stylesheets, $Scripts);
    }
    
}