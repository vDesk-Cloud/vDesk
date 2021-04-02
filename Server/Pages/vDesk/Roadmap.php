<?php
declare(strict_types=1);

namespace Pages\vDesk;

use Pages\vDesk;

/**
 * Roadmap Page class.
 *
 * @package Pages\vDesk
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class Roadmap extends vDesk {
    
    /**
     * The name of the Tutorial.
     *
     * @var string
     */
    public string $Name = "Roadmap";
    
    /**
     * The nav label of the Tutorial
     *
     * @var string
     */
    public string $Description = "Roadmap";
    
    /**
     * Initializes a new instance of the Features Page.
     *
     * @param null|iterable $Values      Initializes the Features Page with the specified Dictionary of values.
     * @param null|iterable $Templates   Initializes the Features Page with the specified Collection of templates.
     * @param null|iterable $Stylesheets Initializes the Features Page with the specified Collection of stylesheets.
     */
    public function __construct(?iterable $Values = [], ?iterable $Templates = ["vDesk/Roadmap"], ?iterable $Stylesheets = ["vDesk/Stylesheet", "vDesk/Packages"]) {
        parent::__construct($Values, $Templates, $Stylesheets);
    }
    
}