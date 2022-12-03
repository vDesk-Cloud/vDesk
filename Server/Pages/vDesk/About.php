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
     * Initializes a new instance of the About Page class.
     *
     * @param null|iterable $Values    Initializes the About Page with the specified Dictionary of values.
     * @param null|iterable $Templates Initializes the About Page with the specified Collection of templates.
     */
    public function __construct(
        ?iterable $Values = [],
        ?iterable $Templates = ["vDesk/About"],
        public string $Name = "About",
        public string $Label = "About",
        public string $Title = "What is vDesk, who's the author and how it started",
        public string $Description = "This document explains the origins of the project and the author behind it"
    ) {
        parent::__construct($Values, $Templates);
    }
    
}