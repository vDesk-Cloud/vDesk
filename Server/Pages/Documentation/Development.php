<?php
declare(strict_types=1);

namespace Pages\Documentation;

use vDesk\Pages\Cached\Page;

/**
 * Development Documentation Page.
 *
 * @package vDesk\Documentation
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class Development extends Page {
    
    /**
     * The name of the Page.
     *
     * @var string
     */
    public string $Name = "Development";
    
    /**
     * The nav label of the Page.
     *
     * @var string
     */
    public string $Description = "Development";
    
    /**
     * Initializes a new instance of the Development Page.
     *
     * @param null|iterable $Values      Initializes the Development Page with the specified Dictionary of values.
     * @param null|iterable $Templates   Initializes the Development Page with the specified Collection of templates.
     * @param null|iterable $Stylesheets Initializes the Development Page with the specified Collection of stylesheets.
     * @param null|iterable $Scripts     Initializes the Development Page with the specified Collection of scripts.
     */
    public function __construct(
        ?iterable $Values = [],
        ?iterable $Templates = ["Documentation/Development"],
        ?iterable $Stylesheets = ["Documentation/Topics"],
        ?iterable $Scripts = []
    ) {
        parent::__construct($Values, $Templates, $Stylesheets, $Scripts);
    }
    
}