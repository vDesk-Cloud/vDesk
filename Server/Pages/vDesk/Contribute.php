<?php
declare(strict_types=1);

namespace Pages\vDesk;

use vDesk\Pages\Cached\Page;

/**
 * Class About
 *
 * @package Pages\vDesk
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class Contribute extends Page {
    
    /**
     * The name of the Tutorial.
     *
     * @var string
     */
    public string $Name = "Contribute";
    
    /**
     * The nav label of the Tutorial
     *
     * @var string
     */
    public string $Description = "Contribute";
    
    /**
     * Initializes a new instance of the About Page.
     *
     * @param null|iterable $Values      Initializes the About Page with the specified Dictionary of values.
     * @param null|iterable $Templates   Initializes the About Page with the specified Collection of templates.
     * @param null|iterable $Stylesheets Initializes the About Page with the specified Collection of stylesheets.
     */
    public function __construct(
        ?iterable $Values = [],
        ?iterable $Templates = ["vDesk/Contribute"],
        ?iterable $Stylesheets = ["vDesk/Stylesheet", "Documentation/Stylesheet"]
    ) {
        parent::__construct($Values, $Templates, $Stylesheets);
    }
    
}