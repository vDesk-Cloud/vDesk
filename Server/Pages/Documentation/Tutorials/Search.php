<?php
declare(strict_types=1);

namespace Pages\Documentation\Tutorials;

use vDesk\Pages\Cached\Page;

/**
 * Class Search
 *
 * @package Pages\Documentation\Tutorials
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class Search extends Page {
    
    /**
     * The name of the Tutorial.
     *
     * @var string
     */
    public string $Name = "Search";
    
    /**
     * The nav label of the Tutorial
     *
     * @var string
     */
    public string $Description = "Searchfilters and -results";
    
    /**
     * Initializes a new instance of the Search Page.
     *
     * @param null|iterable $Values      Initializes the Search Page with the specified Dictionary of values.
     * @param null|iterable $Templates   Initializes the Search Page with the specified Collection of templates.
     * @param null|iterable $Stylesheets Initializes the Search Page with the specified Collection of stylesheets.
     * @param null|iterable $Scripts     Initializes the Search Page with the specified Collection of scripts.
     */
    public function __construct(
        ?iterable $Values = [],
        ?iterable $Templates = ["Documentation/Tutorials/Search"],
        ?iterable $Stylesheets = ["Documentation/Stylesheet", "vDesk/Stylesheet"],
        ?iterable $Scripts = []
    ) {
        parent::__construct($Values, $Templates, $Stylesheets, $Scripts);
    }
    
}