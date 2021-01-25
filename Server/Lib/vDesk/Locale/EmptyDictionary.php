<?php
declare(strict_types=1);

namespace vDesk\Locale;

/**
 * Class EmptyDictionary represents ...
 *
 * @package vDesk\Locale
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 */
final class EmptyDictionary implements \ArrayAccess {
    
    /**
     * The default return value of the EmptyDictionary.
     *
     * @var null
     */
    private $DefaultValue;
    
    /**
     * Initializes a new instance of the EmptyDictionary class.
     *
     * @param null $DefaultValue Initializes the EmptyDictionary with the specified defaultvalue.
     */
    public function __construct($DefaultValue = null) {
        $this->DefaultValue = $DefaultValue;
    }
    
    /**
     * @return mixed
     * @ignore
     */
    public function offsetExists($offset) {
        return true;
    }
    
    /**
     * @return mixed
     * @ignore
     */
    public function offsetGet($offset) {
        return $this->DefaultValue;
    }
    
    /**
     * @ignore
     */
    public function offsetSet($offset, $value) {
        //Do nothing.
    }
    
    /**
     * @ignore
     */
    public function offsetUnset($offset) {
        //Do nothing.
    }
}