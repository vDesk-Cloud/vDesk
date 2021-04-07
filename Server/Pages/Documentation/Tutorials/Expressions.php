<?php
declare(strict_types=1);

namespace Pages\Documentation\Tutorials;

use Pages\Documentation\Tutorials;

/**
 * Expressions Tutorial Page.
 *
 * @package Pages\Tutorials
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class Expressions extends Tutorials {
    
    /**
     * The name of the Tutorial.
     *
     * @var string
     */
    public string $Name = "Expressions";
    
    /**
     * The nav label of the Tutorial
     *
     * @var string
     */
    public string $Description = "Database-Expressions";
    
    /**
     * Initializes a new instance of the Expressions Tutorial.
     *
     * @param null|iterable $Values      Initializes the Expressions Tutorial with the specified Dictionary of values.
     * @param null|iterable $Templates   Initializes the Expressions Tutorial with the specified Collection of templates.
     * @param null|iterable $Stylesheets Initializes the Expressions Tutorial with the specified Collection of stylesheets.
     * @param null|iterable $Scripts     Initializes the Expressions Tutorial with the specified Collection of scripts.
     */
    public function __construct(
        ?iterable $Values = [],
        ?iterable $Templates = ["Documentation/Tutorials/Expressions"],
        ?iterable $Stylesheets = ["Documentation/Stylesheet", "vDesk/Stylesheet"],
        ?iterable $Scripts = [],
    ) {
        parent::__construct($Values, $Templates, $Stylesheets, $Scripts);
    }
    
}