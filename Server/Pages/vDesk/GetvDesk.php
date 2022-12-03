<?php
declare(strict_types=1);

namespace Pages\vDesk;

use vDesk\Pages\Page;

/**
 * GetvDesk Page class.
 *
 * @package Homepage
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class GetvDesk extends Page {

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
        ?iterable $Stylesheets = ["vDesk/Packages", "Documentation/Stylesheet"],
        public string $Name = "GetvDesk",
        public string $Label = "Get vDesk",
        public string $Title = "Download and install vDesk, deploy and host updates",
        public string $Description = "This document describes how to install vDesk and keeping it up to date"
    ) {
        parent::__construct($Values, $Templates, $Stylesheets);
    }
    
}