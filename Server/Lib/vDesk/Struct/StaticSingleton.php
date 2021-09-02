<?php
declare(strict_types=1);

namespace vDesk\Struct;

/**
 * Abstract base class for static singleton classes.
 *
 * @package vDesk
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
abstract class StaticSingleton {

    /**
     * Internal reference storage of StaticSingleton instances.
     * These references are required to prevent instances being instantly collected by the GC.
     *
     * @var array
     */
    private static array $Instances = [];

    /**
     * Initializes the functionality of the StaticSingleton class.
     *
     * @param array $Arguments Initializes the StaticSingleton with specified arguments.
     */
    final public function __construct(...$Arguments) {
        if(!isset(self::$Instances[static::class])) {
            self::$Instances[static::class] = $this;
            static::_construct(...$Arguments);
        }
    }

    /**
     * Initializes the functionality of the StaticSingleton class.
     */
    protected static function _construct() {
    }

    /**
     * Prevent cloning.
     */
    private function __clone() {
    }

}