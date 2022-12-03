<?php
declare(strict_types=1);

namespace Pages\vDesk;

use vDesk\Pages\Cached\Page;

/**
 * Index Page class.
 *
 * @package Homepage
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class Index extends Page {
    
    /**
     * Initializes a new instance of the Index Page.
     *
     * @param null|iterable $Values      Initializes the Index Page with the specified Dictionary of values.
     * @param null|iterable $Templates   Initializes the Index Page with the specified Collection of templates.
     * @param null|iterable $Stylesheets Initializes the Index Page with the specified Collection of stylesheets.
     * @param null|iterable $Scripts     Initializes the Index Page with the specified Collection of scripts.
     * @param array         $Previews    Initializes the Index Page with the specified set of Package preview images.
     */
    public function __construct(
        ?iterable $Values = [],
        ?iterable $Templates = [
            "vDesk/Index"
        ],
        ?iterable $Stylesheets = [
            "Documentation/Stylesheet",
            "vDesk/Animations",
            "vDesk/SlideShow",
            "vDesk/Index",
            "vDesk/Index/Description",
            "vDesk/Index/Features",
            "vDesk/Index/Technology",
            "vDesk/Index/Customization",
            "vDesk/Index/Development"
        ],
        ?iterable $Scripts = ["vDesk/Index", "vDesk/SlideShow"],
        public array $Previews = [],
        public string $Name = "Index",
        public string $Label = "Index",
        public string $Title = "vDesk - Virtual Desktop",
        public string $Description = "vDesk Homepage"
    ) {
        parent::__construct($Values, $Templates, $Stylesheets, $Scripts);
    }
    
}