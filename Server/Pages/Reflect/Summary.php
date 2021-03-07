<?php
declare(strict_types=1);

namespace Pages\Reflect;

use vDesk\Pages\Page;

/**
 * Summary Page class.
 *
 * @package Reflect
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class Summary extends Page {

    /**
     * Initializes a new instance of the Summary Page.
     *
     * @param null|iterable             $Values      Initializes the Summary Page with the specified Dictionary of values.
     * @param null|iterable             $Templates   Initializes the Summary Page with the specified Collection of templates.
     * @param null|iterable             $Stylesheets Initializes the Summary Page with the specified Collection of stylesheets.
     * @param null|iterable             $Scripts     Initializes the Summary Page with the specified Collection of scripts.
     * @param \ReflectionClass[]        $Reflectors  Initializes the Summary Page with the specified set of ReflectionClasses.
     * @param null|\Pages\Reflect\Index $Index       Initializes the Summary Page with the specified Index.
     * @param string[]                  $Errors      Initializes the Summary Page with the specified set of Errors.
     * @param \Throwable[]              $Exceptions  Initializes the Summary Page with the specified set of Exceptions.
     */
    public function __construct(
        ?iterable $Values = [],
        ?iterable $Templates = ["Reflect/Summary"],
        ?iterable $Stylesheets = ["Reflect/Stylesheet"],
        ?iterable $Scripts = [],
        public array $Reflectors = [],
        public ?Index $Index = null,
        public array $Errors = [],
        public array $Exceptions = []
    ) {
        parent::__construct($Values, $Templates, $Stylesheets, $Scripts);
        $this->Index ??= new Index(Reflectors: $Reflectors);
    }

}