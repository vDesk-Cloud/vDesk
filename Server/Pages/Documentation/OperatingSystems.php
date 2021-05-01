<?php
declare(strict_types=1);

namespace Pages\Documentation;

use Pages\Documentation;

/**
 * Packages Documentation
 *
 * @package Pages\Tutorials
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class OperatingSystems extends Documentation {
    
    /**
     * The name of the Tutorial.
     *
     * @var string
     */
    public string $Name = "OperatingSystems";
    
    /**
     * The nav label of the Tutorial
     *
     * @var string
     */
    public string $Description = "Operating systems";
    
    /**
     * Initializes a new instance of the OperatingSystems Page.
     *
     * @param null|iterable $Values      Initializes the OperatingSystems Page with the specified Dictionary of values.
     * @param null|iterable $Templates   Initializes the OperatingSystems Page with the specified Collection of templates.
     * @param null|iterable $Stylesheets Initializes the OperatingSystems Page with the specified Collection of stylesheets.
     * @param null|iterable $Scripts     Initializes the OperatingSystems Page with the specified Collection of scripts.
     * @param array         $Pages       Initializes the OperatingSystems Page with the specified set of Pages.
     */
    public function __construct(
        ?iterable $Values = [],
        ?iterable $Templates = ["Documentation/OperatingSystems"],
        ?iterable $Stylesheets = [],
        ?iterable $Scripts = [],
        array $Pages = []
    ) {
        parent::__construct($Values, $Templates, $Stylesheets, $Scripts, $Pages);
    }
    
}