<?php
declare(strict_types=1);

namespace Pages\Documentation\Server;

/**
 * Server Documentation Page.
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
    public string $Name = "Server";

    /**
     * The nav label of the Page.
     *
     * @var string
     */
    public string $Description = "Server documentation";

    /**
     * Initializes a new instance of the Server Documentation Page.
     *
     * @param null|iterable          $Values      Initializes the Server Documentation Page with the specified Dictionary of values.
     * @param null|iterable          $Templates   Initializes the Server Documentation Page with the specified Collection of templates.
     * @param null|iterable          $Stylesheets Initializes the Server Documentation Page with the specified Collection of stylesheets.
     * @param null|iterable          $Scripts     Initializes the Server Documentation Page with the specified Collection of scripts.
     * @param \vDesk\Pages\Page[]    $Pages       Initializes the Server Documentation Page with the specified set of Pages.
     * @param \vDesk\Pages\Page[]    $Topics      Initializes the Server Documentation Page with the specified set of Topics.
     * @param null|\vDesk\Pages\Page $Topic       Initializes the Server Documentation Page with the specified Topic.
     */
    public function __construct(
        ?iterable                 $Values = [],
        ?iterable                 $Templates = ["Documentation/Server/Page"],
        ?iterable                 $Stylesheets = ["Documentation/Stylesheet", "Documentation/Topics"],
        ?iterable                 $Scripts = [],
        public array              $Pages = [],
        public array              $Topics = [],
        public ?\vDesk\Pages\Page $Topic = null
    ) {
        parent::__construct($Values, $Templates, $Stylesheets, $Scripts);
    }
}