<?php
declare(strict_types=1);

namespace Pages\Reflect;

/**
 * Class TraitPage
 *
 * @package Pages\Reflect
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class TraitPage extends Documentation {

    /**
     *
     * @param null|iterable         $Values
     * @param null|iterable         $Templates
     * @param null|iterable         $Stylesheets
     * @param null|iterable         $Scripts
     * @param null|\ReflectionClass $Reflector
     */
    public function __construct(
        ?iterable $Values = [],
        ?iterable $Templates = ["Reflect/Trait"],
        ?iterable $Stylesheets = ["Reflect/Stylesheet"],
        ?iterable $Scripts = [],
        ?\Reflector $Reflector = null
    ) {
        parent::__construct($Values, $Templates, $Stylesheets, $Scripts, $Reflector);
    }

}