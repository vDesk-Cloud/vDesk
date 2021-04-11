<?php
declare(strict_types=1);

namespace Pages\Documentation;

use Pages\Documentation;
use vDesk\Pages\Page;

/**
 * Class CustomReleases
 *
 * @package Pages\Tutorials
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class Tutorials extends Documentation {
    
    /**
     * The name of the Tutorial.
     *
     * @var string
     */
    public string $Name = "Tutorials";
    
    /**
     * The nav label of the Tutorial
     *
     * @var string
     */
    public string $Description = "Tutorials";
    
    
    /**
     * Initializes a new instance of the Documentation Page.
     *
     * @param null|iterable          $Values      Initializes the Documentation Page with the specified Dictionary of values.
     * @param null|iterable          $Templates   Initializes the Documentation Page with the specified Collection of templates.
     * @param null|iterable          $Stylesheets Initializes the Documentation Page with the specified Collection of stylesheets.
     * @param null|iterable          $Scripts     Initializes the Documentation Page with the specified Collection of scripts.
     * @param string[]               $Pages       Initializes the Documentation Page with the specified set of Pages.
     * @param string[]               $Tutorials   Initializes the Documentation Page with the specified set of Tutorials.
     * @param null|\vDesk\Pages\Page $Tutorial    Initializes the Documentation Page with the specified Tutorial.
     */
    public function __construct(
        ?iterable $Values = [],
        ?iterable $Templates = ["Documentation/Tutorials"],
        ?iterable $Stylesheets = ["Documentation/Stylesheet", "Documentation/Tutorials"],
        ?iterable $Scripts = [],
        array $Pages = [],
        public array $Tutorials = [],
        public ?Page $Tutorial = null
    ) {
        parent::__construct($Values, $Templates, $Stylesheets, $Scripts, $Pages);
    }
    
    
}