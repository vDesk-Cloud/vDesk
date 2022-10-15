<?php
declare(strict_types=1);

namespace Pages\vDesk;

use Pages\vDesk;

/**
 * About Page class.
 *
 * @package Homepage
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class About extends vDesk {
    
    /**
     * The name of the Page.
     *
     * @var string
     */
    public string $Name = "About";
    
    /**
     * The nav label of the Page.
     *
     * @var string
     */
    public string $Description = "About";
    
    /**
     * Initializes a new instance of the About Page class.
     *
     * @param null|iterable $Values    Initializes the About Page with the specified Dictionary of values.
     * @param null|iterable $Templates Initializes the About Page with the specified Collection of templates.
     */
    public function __construct(?iterable $Values = [], ?iterable $Templates = ["vDesk/About"]) {
        parent::__construct($Values, $Templates);
    }
    
}