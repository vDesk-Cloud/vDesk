<?php
declare(strict_types=1);

namespace Pages\Documentation\Client;

use Pages\Documentation\Client;

/**
 * Class Index
 *
 * @package Pages\vDesk
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class Index extends Client {
    
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
    public string $Description = "Tutorials";
    
    /**
     * Initializes a new instance of the Index Page.
     *
     * @param null|iterable $Values      Initializes the Index Page with the specified Dictionary of values.
     * @param null|iterable $Templates   Initializes the Index Page with the specified Collection of templates.
     * @param null|iterable $Stylesheets Initializes the Index Page with the specified Collection of stylesheets.
     * @param null|iterable $Scripts     Initializes the Index Page with the specified Collection of scripts.
     * @param array         $Pages
     * @param array         $Tutorials
     */
    public function __construct(
        ?iterable $Values = [],
        ?iterable $Templates = ["Documentation/Client/Index"],
        ?iterable $Stylesheets = [],
        ?iterable $Scripts = [],
        public array $Pages = [],
        public array $Tutorials = [],
    ) {
        parent::__construct($Values, $Templates, $Stylesheets, $Scripts, $Pages, $Tutorials);
    }
    
}