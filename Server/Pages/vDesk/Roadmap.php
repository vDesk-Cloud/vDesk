<?php
declare(strict_types=1);

namespace Pages\vDesk;

use Pages\vDesk;

/**
 * Roadmap Page class.
 *
 * @package Homepage
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class Roadmap extends vDesk {

    /**
     * Initializes a new instance of the Roadmap Page class.
     *
     * @param null|iterable $Values      Initializes the Roadmap Page with the specified Dictionary of values.
     * @param null|iterable $Templates   Initializes the Roadmap Page with the specified Collection of templates.
     * @param null|iterable $Stylesheets Initializes the Roadmap Page with the specified Collection of stylesheets.
     */
    public function __construct(
        ?iterable $Values = [],
        ?iterable $Templates = ["vDesk/Roadmap"],
        ?iterable $Stylesheets = [],
        public string $Name = "Roadmap",
        public string $Label = "Roadmap",
        public string $Title = "vDesk's roadmap of planned features",
        public string $Description = "This document contains a roadmap of planned features or deprecations for the future"
    ) {
        parent::__construct($Values, $Templates, $Stylesheets);
    }
    
}