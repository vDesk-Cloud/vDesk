<?php
declare(strict_types=1);

namespace Pages\Documentation;

use vDesk\Pages\Cached\Page;

/**
 * Custom Releases Documentation Page.
 *
 * @package vDesk\Documentation
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class GlobalEventSystem extends Page {
    
    /**
     * The name of the Page.
     *
     * @var string
     */
    public string $Name = "GlobalEventSystem";
    
    /**
     * The nav label of the Page.
     *
     * @var string
     */
    public string $Description = "Global event system";

    /** @inheritDoc */
    public function __construct(
        ?iterable $Values = [],
        ?iterable $Templates = ["Documentation/GlobalEventSystem"],
        ?iterable $Stylesheets = [],
        ?iterable $Scripts = [],
    ) {
        parent::__construct($Values, $Templates, $Stylesheets, $Scripts);
    }
}