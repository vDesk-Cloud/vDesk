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
class GlobalEventSystem extends Page {
    
    /**
     * The name of the Tutorial.
     *
     * @var string
     */
    public string $Name = "GlobalEventSystem";
    
    /**
     * The nav label of the Tutorial
     *
     * @var string
     */
    public string $Description = "Global event system";
    
    /**
     * Initializes a new instance of the GlobalEventSystem Tutorial.
     *
     * @param null|iterable $Values      Initializes the GlobalEventSystem Tutorial with the specified Dictionary of values.
     * @param null|iterable $Templates   Initializes the GlobalEventSystem Tutorial with the specified Collection of templates.
     * @param null|iterable $Stylesheets Initializes the GlobalEventSystem Tutorial with the specified Collection of stylesheets.
     * @param null|iterable $Scripts     Initializes the GlobalEventSystem Tutorial with the specified Collection of scripts.
     */
    public function __construct(
        ?iterable $Values = [],
        ?iterable $Templates = ["Documentation/Tutorials/GlobalEventSystem"],
        ?iterable $Stylesheets = [],
        ?iterable $Scripts = [],
    ) {
        parent::__construct($Values, $Templates, $Stylesheets, $Scripts);
    }
    
}