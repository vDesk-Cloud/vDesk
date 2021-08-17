<?php
declare(strict_types=1);

namespace Pages\vDesk;

use Pages\vDesk;

/**
 * Donate Page class.
 *
 * @package Homepage
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class Donate extends vDesk {
    
    /**
     * The name of the Page.
     *
     * @var string
     */
    public string $Name = "Donate";
    
    /**
     * The nav label of the Page.
     *
     * @var string
     */
    public string $Description = "Donate";
    
    /**
     * Initializes a new instance of the Donate Page class.
     *
     * @param null|iterable $Values    Initializes the Donate Page with the specified Dictionary of values.
     * @param null|iterable $Templates Initializes the Donate Page with the specified Collection of templates.
     */
    public function __construct(?iterable $Values = [], ?iterable $Templates = ["vDesk/Donate"]) {
        parent::__construct($Values, $Templates);
    }
    
}