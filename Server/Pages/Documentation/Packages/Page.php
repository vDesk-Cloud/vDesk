<?php
declare(strict_types=1);

namespace Pages\Documentation\Packages;

/**
 * Server Documentation Page.
 *
 * @package vDesk\Documentation
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class Page extends \vDesk\Pages\Page {

    /**
     * Initializes a new instance of the Packages Documentation Page.
     *
     * @param null|iterable          $Values      Initializes the Packages Documentation Page with the specified Dictionary of values.
     * @param null|iterable          $Templates   Initializes the Packages Documentation Page with the specified Collection of templates.
     * @param null|iterable          $Stylesheets Initializes the Packages Documentation Page with the specified Collection of stylesheets.
     * @param null|iterable          $Scripts     Initializes the Packages Documentation Page with the specified Collection of scripts.
     * @param \vDesk\Pages\Page[]    $Pages       Initializes the Packages Documentation Page with the specified set of Pages.
     * @param \vDesk\Pages\Page[]    $Packages    Initializes the Packages Documentation Page with the specified set of Packages.
     * @param null|\vDesk\Pages\Page $Package     Initializes the Packages Documentation Page with the specified Package.
     */
    public function __construct(
        ?iterable $Values = [],
        ?iterable $Templates = ["Documentation/Packages/Page"],
        ?iterable $Stylesheets = ["Documentation/Stylesheet", "Documentation/Packages", "Documentation/Code"],
        ?iterable $Scripts = [],
        public array $Pages = [],
        public array $Packages = [],
        public ?\vDesk\Pages\Page $Package = null,
        public string $Name = "PackagesDocumentation",
        public string $Label = "Packages documentation",
        public string $Title = "vDesk - Packages documentation",
        public string $Description = "Packages documentation"
    ) {
        parent::__construct($Values, $Templates, $Stylesheets, $Scripts);
    }
}