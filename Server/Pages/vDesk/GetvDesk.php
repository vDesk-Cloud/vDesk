<?php
declare(strict_types=1);

namespace Pages\vDesk;

use vDesk\Pages\Cached\Page;

/**
 * GetvDesk Page class.
 *
 * @package Homepage
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class GetvDesk extends Page {
    
    /**
     * The name of the Page.
     *
     * @var string
     */
    public string $Name = "GetvDesk";
    
    /**
     * The nav label of the Page.
     *
     * @var string
     */
    public string $Description = "Get vDesk";
    
    /**
     * Initializes a new instance of the GetvDesk Page class.
     *
     * @param null|iterable $Values      Initializes the GetvDesk Page with the specified Dictionary of values.
     * @param null|iterable $Templates   Initializes the GetvDesk Page with the specified Collection of templates.
     * @param null|iterable $Stylesheets Initializes the GetvDesk Page with the specified Collection of stylesheets.
     */
    public function __construct(
        ?iterable $Values = [],
        ?iterable $Templates = ["vDesk/GetvDesk"],
        ?iterable $Stylesheets = ["vDesk/Packages", "Documentation/Stylesheet"]
    ) {
        parent::__construct($Values, $Templates, $Stylesheets);
    }
    
}