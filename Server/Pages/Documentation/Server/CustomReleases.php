<?php
declare(strict_types=1);

namespace Pages\Documentation\Server;

use vDesk\Pages\Cached\Page;

/**
 * Class CustomReleases
 *
 * @package Pages\Tutorials
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class CustomReleases extends Page {
    
    /**
     * The name of the Tutorial.
     *
     * @var string
     */
    public string $Name = "CustomReleases";
    
    /**
     * The nav label of the Tutorial
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