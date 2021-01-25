<?php
declare(strict_types=1);

namespace Pages;

use vDesk\Pages\Page;

/**
 * Documentation Page class.
 *
 * @package Pages
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
abstract class Documentation extends Page {
    
    /**
     * @var string
     */
    public string $Name = "Documentation";
    
    /**
     * The nav label of the Tutorial
     *
     * @var string
     */
    public string $Description = "Documentation";
    
    /**
     * Initializes a new instance of the Documentation Page.
     *
     * @param null|iterable $Values      Initializes the Documentation Page with the specified Dictionary of values.
     * @param null|iterable $Templates   Initializes the Documentation Page with the specified Collection of templates.
     * @param null|iterable $Stylesheets Initializes the Documentation Page with the specified Collection of stylesheets.
     * @param null|iterable $Scripts     Initializes the Documentation Page with the specified Collection of scripts.
     */
    public function __construct(?iterable $Values = [], ?iterable $Templates = ["Documentation"], ?iterable $Stylesheets = ["Documentation/Stylesheet", "vDesk/Stylesheet"], ?iterable $Scripts = []) {
        parent::__construct($Values, $Templates, $Stylesheets, $Scripts);
    }
    
}