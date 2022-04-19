<?php
declare(strict_types=1);

namespace Pages\Documentation\Server;

use vDesk\Pages\Cached\Page;

/**
 * Custom Releases Documentation Page.
 *
 * @package vDesk\Documentation
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class CustomReleases extends Page {
    
    /**
     * The name of the Page.
     *
     * @var string
     */
    public string $Name = "CustomReleases";
    
    /**
     * The nav label of the Page.
     *
     * @var string
     */
    public string $Description = "Custom releases and packages";

    /** @inheritDoc */
    public function __construct(
        ?iterable $Values = [],
        ?iterable $Templates = ["Documentation/Server/CustomReleases"],
        ?iterable $Stylesheets = [],
        ?iterable $Scripts = []
    ) {
        parent::__construct($Values, $Templates, $Stylesheets, $Scripts);
    }
}