<?php
declare(strict_types=1);

namespace Pages\Documentation\Server;

use vDesk\Pages\Cached\Page;

/**
 * Machines Documentation Page.
 *
 * @package vDesk\Documentation
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class Machines extends Page {

    /**
     * The name of the Page.
     *
     * @var string
     */
    public string $Name = "Machines";

    /**
     * The nav label of the Page.
     *
     * @var string
     */
    public string $Description = "Machines";

    /** @inheritDoc */
    public function __construct(
        ?iterable $Values = [],
        ?iterable $Templates = ["Documentation/Server/Machines"],
        ?iterable $Stylesheets = [],
        ?iterable $Scripts = []
    ) {
        parent::__construct($Values, $Templates, $Stylesheets, $Scripts);
    }
}