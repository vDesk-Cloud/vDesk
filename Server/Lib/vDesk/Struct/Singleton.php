<?php
declare(strict_types=1);

namespace vDesk\Struct;

/**
 * Abstract base class for singleton classes.
 *
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 */
abstract class Singleton {
    
    /**
     * Internal reference storage of Singleton instances.
     * These references are required to prevent instances being instantly collected by the GC.
     *
     * @var array
     */
    private static array $Instances = [];
    
    /**
     * Initializes the functionality of the Singleton class.
     */
    public final function __construct() {
        if(!isset(self::$Instances[static::class])) {
            self::$Instances[static::class] = $this;
            $this->_construct();
        }
    }
    
    /**
     * Gets an unique instance of the extending class.
     *
     * @return mixed An unique instance of the extending class.
     */
    public final static function &GetInstance() {
        if(!isset(self::$Instances[static::class])) {
            new static();
        }
        return self::$Instances[static::class];
    }
    
    /**
     * Proxies static method calls to the instance of the Singleton.
     *
     * @param string $Method    The method to call.
     * @param array  $Arguments The arguments to pass to the specified method.
     *
     * @return mixed The return value of the called method.
     */
    public static function __callStatic($Method, $Arguments) {
        return self::GetInstance()->{$Method}(...$Arguments);
    }
    
    /**
     * Initializes the functionality of the Singleton class.
     */
    protected function _construct() {
    }
    
}