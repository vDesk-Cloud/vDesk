<?php
declare(strict_types=1);

namespace Pages\Documentation\Tutorials;

use Pages\Documentation\Tutorials;
use vDesk\Pages\Page;

/**
 * Class Index
 *
 * @package Pages\vDesk
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class Index extends Tutorials {
    
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
     * @param null|iterable          $Values      Initializes the Index Page with the specified Dictionary of values.
     * @param null|iterable          $Templates   Initializes the Index Page with the specified Collection of templates.
     * @param null|iterable          $Stylesheets Initializes the Index Page with the specified Collection of stylesheets.
     * @param null|iterable          $Scripts     Initializes the Index Page with the specified Collection of scripts.
     * @param array                  $Pages
     * @param array                  $Tutorials
     * @param null|\vDesk\Pages\Page $Tutorial
     */
    public function __construct(
        ?iterable $Values = [],
        ?iterable $Templates = ["Documentation/Tutorials/Index"],
        ?iterable $Stylesheets = ["Documentation/Stylesheet", "vDesk/Stylesheet"],
        ?iterable $Scripts = [],
        public array $Pages = [],
        public array $Tutorials = [],
        public ?Page $Tutorial = null
    ) {
        parent::__construct($Values, $Templates, $Stylesheets, $Scripts, $Pages, $Tutorials, $Tutorial);
    }
    
}