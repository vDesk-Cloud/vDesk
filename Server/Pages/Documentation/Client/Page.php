<?php
declare(strict_types=1);

namespace Pages\Documentation\Client;

/**
 * Client Documentation Page.
 *
 * @package vDesk\Documentation
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class Page extends \vDesk\Pages\Page {

    /**
     * The name of the Page.
     *
     * @var string
     */
    public string $Name = "Client";

    /**
     * The nav label of the Page.
     *
     * @var string
     */
    public string $Description = "Client documentation";

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
        ?iterable                 $Values = [],
        ?iterable                 $Templates = ["Documentation/Client/Page"],
        ?iterable                 $Stylesheets = ["Documentation/Stylesheet", "Documentation/Topics"],
        ?iterable                 $Scripts = [],
        public array              $Pages = [],
        public array              $Topics = [],
        public ?\vDesk\Pages\Page $Topic = null
    ) {
        parent::__construct($Values, $Templates, $Stylesheets, $Scripts);
    }
}