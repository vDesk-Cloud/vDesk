<?php
declare(strict_types=1);

namespace Pages\vDesk;

/**
 * The download Page of the vDesk homepage.
 *
 * @package Pages\vDesk
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class GetvDesk extends Packages {
    
    /**
     * The name of the Tutorial.
     *
     * @var string
     */
    public string $Name = "GetvDesk";
    
    /**
     * The nav label of the Tutorial
     *
     * @var string
     */
    public string $Description = "Get vDesk";
    
    /**
     * Initializes a new instance of the Download Page.
     *
     * @param null|iterable $Values      Initializes the Download Page with the specified Dictionary of values.
     * @param null|iterable $Templates   Initializes the Download Page with the specified Collection of templates.
     * @param null|iterable $Stylesheets Initializes the Download Page with the specified Collection of stylesheets.
     * @param null|iterable $Scripts     Initializes the Download Page with the specified Collection of scripts.
     */
    public function __construct(?iterable $Values = [], ?iterable $Templates = [], ?iterable $Stylesheets = ["vDesk/Stylesheet", "Documentation/Stylesheet", "vDesk/Packages"], ?iterable $Scripts = []) {
        parent::__construct($Values, $Templates, $Stylesheets, $Scripts);
    }
    
}