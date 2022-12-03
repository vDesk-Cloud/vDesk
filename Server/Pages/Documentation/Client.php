<?php
declare(strict_types=1);

namespace Pages\Documentation;

use Pages\Documentation;
use vDesk\Pages\Page;

/**
 * Client Documentation Page.
 *
 * @package vDesk\Documentation
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class Client extends Documentation {


    /**
     * Initializes a new instance of the Client Documentation Page.
     *
     * @param null|iterable          $Values      Initializes the Client Documentation Page with the specified Dictionary of values.
     * @param null|iterable          $Templates   Initializes the Client Documentation Page with the specified Collection of templates.
     * @param null|iterable          $Stylesheets Initializes the Client Documentation Page with the specified Collection of stylesheets.
     * @param null|iterable          $Scripts     Initializes the Client Documentation Page with the specified Collection of scripts.
     * @param \vDesk\Pages\Page[]    $Pages       Initializes the Client Documentation Page with the specified set of Pages.
     * @param \vDesk\Pages\Page[]    $Topics      Initializes the Client Documentation Page with the specified set of Topics.
     * @param null|\vDesk\Pages\Page $Topic       Initializes the Client Documentation Page with the specified Topic.
     */
    public function __construct(
        ?iterable    $Values = [],
        ?iterable    $Templates = ["Documentation/Client"],
        ?iterable    $Stylesheets = ["Documentation/Stylesheet", "Documentation/Topics"],
        ?iterable    $Scripts = [],
        array        $Pages = [],
        public array $Topics = [],
        public ?Page $Topic = null,
        public string $Name = "Client",
        public string $Label = "Client documentation",
        public string $Title = "Client documentation",
        public string $Description = "Client documentation"
    ) {
        parent::__construct($Values, $Templates, $Stylesheets, $Scripts, $Pages);
    }
}