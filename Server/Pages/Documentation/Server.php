<?php
declare(strict_types=1);

namespace Pages\Documentation;

use Pages\Documentation;
use vDesk\Pages\Page;

/**
 * Server Documentation Page.
 *
 * @package vDesk\Documentation
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class Server extends Documentation {

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
     * Initializes a new instance of the Client Documentation Page.
     *
     * @param null|iterable          $Values      Initializes the Server Documentation Page with the specified Dictionary of values.
     * @param null|iterable          $Templates   Initializes the Server Documentation Page with the specified Collection of templates.
     * @param null|iterable          $Stylesheets Initializes the Server Documentation Page with the specified Collection of stylesheets.
     * @param null|iterable          $Scripts     Initializes the Server Documentation Page with the specified Collection of scripts.
     * @param string[]               $Pages       Initializes the Server Documentation Page with the specified set of Pages.
     * @param string[]               $Topics      Initializes the Server Documentation Page with the specified set of Topics.
     * @param null|\vDesk\Pages\Page $Topic       Initializes the Server Documentation Page with the specified Topic.
     */
    public function __construct(
        ?iterable    $Values = [],
        ?iterable    $Templates = ["Documentation/Server"],
        ?iterable    $Stylesheets = ["Documentation/Stylesheet", "Documentation/Topics"],
        ?iterable    $Scripts = [],
        array        $Pages = [],
        public array $Topics = [],
        public ?Page $Topic = null
    ) {
        parent::__construct($Values, $Templates, $Stylesheets, $Scripts, $Pages);
    }
}