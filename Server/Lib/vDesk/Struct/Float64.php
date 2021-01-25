<?php
declare(strict_types=1);

namespace vDesk\Struct;

/**
 * Class Float64 represents ...
 *
 * @package vDesk\Struct
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 */
class Float64 {
    
    /**
     * the highest possible number an 64-bit floating point number can support.
     */
    public const Max = 1.7976931348623e+308;
    
    /**
     * the lowest possible number an 64-bit floating point number can support.
     */
    public const Min = 2.2250738585072e-308;
    
    /**
     * The size of bytes an 64-bit floating point number will address.
     */
    public const Size = 8;
    
}