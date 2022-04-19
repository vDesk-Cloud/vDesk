<?php
declare(strict_types=1);

namespace Pages\Documentation\Server;

use vDesk\Pages\Cached\Page;

class Machines extends Page {

    /**
     * The name of the Topic.
     *
     * @var string
     */
    public string $Name = "Machines";

    /**
     * The nav label of the Topic.
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