<?php
declare(strict_types=1);

namespace Pages;

use vDesk\Pages\Page;

/**
 * vDesk Page class.
 *
 * @package Pages
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
abstract class vDesk extends Page {
    
    /**
     * The name of the Tutorial.
     *
     * @var string
     */
    public string $Name = "Index";
    
    /**
     * The nav label of the Tutorial
     *
     * @var string
     */
    public string $Description = "vDesk";
    
    /**
     * Initializes a new instance of the vDesk class.
     *
     * @param null|iterable $Values      Initializes the vDesk Page with the specified Dictionary of values.
     * @param null|iterable $Templates   Initializes the vDesk Page with the specified Collection of templates.
     * @param null|iterable $Stylesheets Initializes the vDesk Page with the specified Collection of stylesheets.
     * @param null|iterable $Scripts     Initializes the vDesk Page with the specified Collection of scripts.
     */
    public function __construct(?iterable $Values = [], ?iterable $Templates = ["vDesk"], ?iterable $Stylesheets = ["vDesk/Stylesheet"], ?iterable $Scripts = []) {
        parent::__construct($Values, $Templates, $Stylesheets, $Scripts);
    }
}