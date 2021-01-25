<?php
declare(strict_types=1);

namespace vDesk\DataProvider;

/**
 * Enumeration of bit set flags of available database collations.
 *
 * @package vDesk\Connection\DataProvider
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
abstract class Collation {
    
    /**
     * Charset Ascii.
     */
    public const ASCII = 0b00001;
    
    /**
     * UTF8
     */
    public const UTF8 = 0b00010;
    
    /**
     * UTF16
     */
    public const UTF16 = 0b00100;
    
    /**
     * UTF32
     */
    public const UTF32 = 0b01000;
    
    /**
     * Flag indicating whether the collation is binary.
     */
    public const Binary = 0b10000;
    
}