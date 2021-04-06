<?php
declare(strict_types=1);

namespace Pages\Documentation;

use Pages\Documentation;

/**
 * Packages Documentation
 *
 * @package Pages\Tutorials
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class Packages extends Documentation {
    
    /**
     * The name of the Tutorial.
     *
     * @var string
     */
    public string $Name = "Packages";
    
    /**
     * The nav label of the Tutorial
     *
     * @var string
     */
    public string $Description = "Packages";
    
    /**
     * Initializes a new instance of the Packages Page.
     *
     * @param null|iterable $Values      Initializes the Packages Page with the specified Dictionary of values.
     * @param null|iterable $Templates   Initializes the Packages Page with the specified Collection of templates.
     * @param null|iterable $Stylesheets Initializes the Packages Page with the specified Collection of stylesheets.
     * @param null|iterable $Scripts     Initializes the Packages Page with the specified Collection of scripts.
     * @param array         $Pages       Initializes the Packages Page with the specified set of Pages.
     */
    public function __construct(
        ?iterable $Values = [],
        ?iterable $Templates = ["Documentation/Packages"],
        ?iterable $Stylesheets = ["Documentation/Stylesheet", "vDesk/Stylesheet"],
        ?iterable $Scripts = [],
        array $Pages = []
    ) {
        parent::__construct($Values, $Templates, $Stylesheets, $Scripts, $Pages);
    }
    
}