<?php
declare(strict_types=1);

namespace Pages\Documentation;

use vDesk\Pages\Cached\Page;

/**
 * Security Documentation Page.
 *
 * @package vDesk\Documentation
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class Security extends Page {


    /**
     * The nav label of the Page.
     *
     * @var string
     */

    /**
     * Initializes a new instance of the Security Page.
     *
     * @param null|iterable $Values      Initializes the Security Page with the specified Dictionary of values.
     * @param null|iterable $Templates   Initializes the Security Page with the specified Collection of templates.
     * @param null|iterable $Stylesheets Initializes the Security Page with the specified Collection of stylesheets.
     * @param null|iterable $Scripts     Initializes the Security Page with the specified Collection of scripts.
     * @param string        $Name        Initializes the Security Page with the specified name.
     * @param string        $Label       Initializes the Security Page with the specified nav label.
     * @param string        $Title       Initializes the Security Page with the specified title.
     * @param string        $Description Initializes the Security Page with the specified description.
     */
    public function __construct(
        ?iterable $Values = [],
        ?iterable $Templates = ["Documentation/Security"],
        ?iterable $Stylesheets = ["Documentation/Topics"],
        ?iterable $Scripts = [],
        public string $Name = "Security",
        public string $Label = "Security",
        public string $Title = "Security concepts of vDesk",
        public string $Description = "Security",
    ) {
        parent::__construct($Values, $Templates, $Stylesheets, $Scripts);
    }

}