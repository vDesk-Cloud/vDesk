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
class vDesk extends Page {
    
    /**
     * The name of the Page.
     *
     * @var string
     */
    public string $Name = "vDesk";
    
    /**
     * The nav label of the Page
     *
     * @var string
     */
    public string $Description = "vDesk";
    
    /**
     * Initializes a new instance of the vDesk Page class.
     *
     * @param null|iterable          $Values      Initializes the vDesk Page with the specified Dictionary of values.
     * @param null|iterable          $Templates   Initializes the vDesk Page with the specified Collection of templates.
     * @param null|iterable          $Stylesheets Initializes the vDesk Page with the specified Collection of stylesheets.
     * @param null|iterable          $Scripts     Initializes the vDesk Page with the specified Collection of scripts.
     * @param string[]               $Pages       Initializes the vDesk Page with the specified set of Pages.
     * @param null|\vDesk\Pages\Page $Content     Initializes the vDesk Page with the specified content Page.
     */
    public function __construct(
        ?iterable $Values = [],
        ?iterable $Templates = ["vDesk"],
        ?iterable $Stylesheets = ["vDesk/Stylesheet"],
        ?iterable $Scripts = [],
        public array $Pages = [],
        public ?Page $Content = null
    ) {
        parent::__construct($Values, $Templates, $Stylesheets, $Scripts);
    }
}