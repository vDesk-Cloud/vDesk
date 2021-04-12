<?php
declare(strict_types=1);

namespace Pages\Documentation\Tutorials;

use vDesk\Pages\Cached\Page;

/**
 * Class ClassicalInheritance
 *
 * @package Pages\Documentation
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class ClassicalInheritance extends Page {
    
    /**
     * The name of the Tutorial.
     *
     * @var string
     */
    public string $Name = "ClassicalInheritance";
    
    /**
     * The nav label of the Tutorial
     *
     * @var string
     */
    public string $Description = "Classical inheritance and Interfaces";
    
    /**
     * Initializes a new instance of the ClassicalInheritance Tutorial.
     *
     * @param null|iterable $Values      Initializes the ClassicalInheritance Tutorial with the specified Dictionary of values.
     * @param null|iterable $Templates   Initializes the ClassicalInheritance Tutorial with the specified Collection of templates.
     * @param null|iterable $Stylesheets Initializes the ClassicalInheritance Tutorial with the specified Collection of stylesheets.
     * @param null|iterable $Scripts     Initializes the ClassicalInheritance Tutorial with the specified Collection of scripts.
     */
    public function __construct(
        ?iterable $Values = [],
        ?iterable $Templates = ["Documentation/Tutorials/ClassicalInheritance"],
        ?iterable $Stylesheets = ["Documentation/Stylesheet"],
        ?iterable $Scripts = [],
    ) {
        parent::__construct($Values, $Templates, $Stylesheets, $Scripts);
    }
    
}