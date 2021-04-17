<?php
declare(strict_types=1);

namespace Pages\vDesk;

use vDesk\Pages\Cached\Page;

/**
 * Contribute Page class.
 *
 * @package Homepage
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class Contribute extends Page {
    
    /**
     * The name of the Page.
     *
     * @var string
     */
    public string $Name = "Contribute";
    
    /**
     * The nav label of the Page.
     *
     * @var string
     */
    public string $Description = "Contribute";
    
    /**
     * Initializes a new instance of the Contribute Page.
     *
     * @param null|iterable $Values      Initializes the Contribute Page with the specified Dictionary of values.
     * @param null|iterable $Templates   Initializes the Contribute Page with the specified Collection of templates.
     * @param null|iterable $Stylesheets Initializes the Contribute Page with the specified Collection of stylesheets.
     */
    public function __construct(
        ?iterable $Values = [],
        ?iterable $Templates = ["vDesk/Contribute"],
        ?iterable $Stylesheets = ["Documentation/Stylesheet"]
    ) {
        parent::__construct($Values, $Templates, $Stylesheets);
    }
    
}