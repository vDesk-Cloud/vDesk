<?php
declare(strict_types=1);

namespace Pages\vDesk;

use Pages\vDesk;

/**
 * Class About
 *
 * @package Pages\vDesk
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class Contribute extends vDesk {
    
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
     * @param null|iterable $Scripts     Initializes the About Page with the specified Collection of scripts.
     */
    public function __construct(?iterable $Values = [], ?iterable $Templates = ["vDesk/Contribute"], ?iterable $Stylesheets = ["vDesk/Stylesheet", "Documentation/Stylesheet"], ?iterable $Scripts = []) {
        parent::__construct($Values, $Templates, $Stylesheets, $Scripts);
    }
}