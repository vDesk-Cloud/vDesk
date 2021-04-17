<?php
declare(strict_types=1);

namespace Pages\vDesk;

use vDesk\Pages\Cached\Page;

/**
 * Packages Page class.
 *
 * @package Homepage
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class Packages extends Page {
    
    /**
     * The name of the Page.
     *
     * @var string
     */
    public string $Name = "Packages";
    
    /**
     * The nav label of the Page.
     *
     * @var string
     */
    public string $Description = "Packages";
    
    /**
     * Initializes a new instance of the Packages Page class.
     *
     * @param null|iterable $Values      Initializes the Packages Page with the specified Dictionary of values.
     * @param null|iterable $Templates   Initializes the Packages Page with the specified Collection of templates.
     * @param null|iterable $Stylesheets Initializes the Packages Page with the specified Collection of stylesheets.
     */
    public function __construct(
        ?iterable $Values = [],
        ?iterable $Templates = ["vDesk/Packages"],
        ?iterable $Stylesheets = ["vDesk/Packages"]
    ) {
        parent::__construct($Values, $Templates, $Stylesheets);
    }
    
}