<?php
declare(strict_types=1);

namespace Pages\Documentation;

use vDesk\Pages\Page;

/**
 * Architecture Documentation Page.
 *
 * @package vDesk\Documentation
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class Architecture extends Page {
    
    /**
     * The name of the Page.
     *
     * @var string
     */
    public string $Name = "Architecture";
    
    /**
     * The nav label of the Page.
     *
     * @var string
     */
    public string $Description = "Architecture";

    /** @inheritDoc */
    public function __construct(
        ?iterable $Values = [],
        ?iterable $Templates = ["Documentation/Architecture"],
        ?iterable $Stylesheets = ["Documentation/Topics"],
        ?iterable $Scripts = []
    ) {
        parent::__construct($Values, $Templates, $Stylesheets, $Scripts);
    }
}