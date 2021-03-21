<?php
declare(strict_types=1);

namespace Pages\vDesk;

use Pages\vDesk;

/**
 * Class Index
 *
 * @package Pages\vDesk
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class Index extends vDesk {
    
    /**
     * The name of the Tutorial.
     *
     * @var string
     */
    public string $Name = "Index";
    
    /**
     * The nav label of the Tutorial
     *
     * @var string
     */
    public string $Description = "vDesk Index page";
    
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
            "vDesk/Index/Description",
            "vDesk/Index/Features",
            "vDesk/Index/Technology",
            "vDesk/Index/Customization",
            "vDesk/Index/Development"
        ],
        ?iterable $Stylesheets = ["vDesk/Stylesheet", "Documentation/Stylesheet", "vDesk/Index", "vDesk/Animations", "vDesk/SlideShow"],
        ?iterable $Scripts = ["vDesk/Index", "vDesk/SlideShow"],
        public array $Previews = []
    ) {
        parent::__construct($Values, $Templates, $Stylesheets, $Scripts);
    }
    
}