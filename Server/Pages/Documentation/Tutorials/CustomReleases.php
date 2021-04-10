<?php
declare(strict_types=1);

namespace Pages\Documentation\Tutorials;

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
    
    /**
     * Initializes a new instance of the CustomReleases Tutorial.
     *
     * @param null|iterable $Values      Initializes the CustomReleases Tutorial with the specified Dictionary of values.
     * @param null|iterable $Templates   Initializes the CustomReleases Tutorial with the specified Collection of templates.
     * @param null|iterable $Stylesheets Initializes the CustomReleases Tutorial with the specified Collection of stylesheets.
     * @param null|iterable $Scripts     Initializes the CustomReleases Tutorial with the specified Collection of scripts.
     */
    public function __construct(
        ?iterable $Values = [],
        ?iterable $Templates = ["Documentation/Tutorials/CustomReleases"],
        ?iterable $Stylesheets = ["Documentation/Stylesheet","vDesk/Stylesheet", "vDesk/Packages"],
        ?iterable $Scripts = []
    ) {
        parent::__construct($Values, $Templates, $Stylesheets, $Scripts);
    }
    
}