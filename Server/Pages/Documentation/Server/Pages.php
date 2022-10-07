<?php
declare(strict_types=1);

namespace Pages\Documentation\Server;

use vDesk\Pages\Cached\Page;

/**
 * Pages Documentation Page.
 *
 * @package vDesk\Documentation
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class Pages extends Page {
    
    /**
     * The name of the Page.
     *
     * @var string
     */
    public string $Name = "Pages";
    
    /**
     * The nav label of the Page.
     *
     * @var string
     */
    public string $Description = "Pages";
    
    /**
     * Initializes a new instance of the Pages Page.
     *
     * @param null|iterable $Values      Initializes the Pages Page with the specified Dictionary of values.
     * @param null|iterable $Templates   Initializes the Pages Page with the specified Collection of templates.
     * @param null|iterable $Stylesheets Initializes the Pages Page with the specified Collection of stylesheets.
     * @param null|iterable $Scripts     Initializes the Pages Page with the specified Collection of scripts.
     */
    public function __construct(
        ?iterable $Values = [],
        ?iterable $Templates = ["Documentation/Server/Pages"],
        ?iterable $Stylesheets = [],
        ?iterable $Scripts = []
    ) {
        parent::__construct($Values, $Templates, $Stylesheets, $Scripts);
    }
    
}