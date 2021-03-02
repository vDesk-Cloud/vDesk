<?php
declare(strict_types=1);

namespace Pages\Reflect;

use vDesk\Pages\Page;

class Index extends Page {

    /**
     *
     * @param null|iterable $Values
     * @param null|iterable $Templates
     * @param null|iterable $Stylesheets
     * @param null|iterable $Scripts
     * @param array         $Reflectors
     */
    public function __construct(
        ?iterable $Values = [],
        ?iterable $Templates = ["Reflect/Index"],
        ?iterable $Stylesheets = ["Reflect/Stylesheet"],
        ?iterable $Scripts = [],
        public array $Reflectors = []
    ) {
        parent::__construct($Values, $Templates, $Stylesheets, $Scripts);
    }

}