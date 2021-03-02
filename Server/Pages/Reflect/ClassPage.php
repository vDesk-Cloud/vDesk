<?php
declare(strict_types=1);

namespace Pages\Reflect;

/**
 * Class Documentation
 *
 * @package Pages\Reflect\Class
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class ClassPage extends Documentation {

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
        ?iterable $Templates = ["Reflect/Class"],
        ?iterable $Stylesheets = ["Reflect/Stylesheet"],
        ?iterable $Scripts = [],
        ?\Reflector $Reflector = null,
        public bool $Final = false,
        public bool $Abstract = false
    ) {
        parent::__construct($Values, $Templates, $Stylesheets, $Scripts, $Reflector);
        $this->Final = $Reflector->isFinal();
        $this->Abstract = $Reflector->isAbstract();
    }

}
