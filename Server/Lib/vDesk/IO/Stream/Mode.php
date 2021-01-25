<?php

declare(strict_types=1);

namespace vDesk\IO\Stream;

/**
 * Enumeration of specifications how the operating system should open a file.
 *
 * @package vDesk\IO\Stream
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 */
abstract class Mode {
    
    /**
     * Open for reading only; place the file pointer at the beginning of the file.
     */
    public const Read = 0b000001;
    
    /**
     * Open for writing only; place the file pointer at the beginning of the file and truncate the file to zero length.
     * If the file does not exist, attempt to create it.
     */
    public const Truncate = 0b000010;
    
    /**
     * Open for writing only; place the file pointer at the end of the file.
     * If the file does not exist, attempt to create it.
     * In this mode, {@see \vDesk\IO\IStream::Seek()} has no effect, writes are always appended.
     */
    public const Append = 0b000100;
    
    /**
     * Create and open for writing only; place the file pointer at the beginning of the file.
     * If the file already exists, the {@see \vDesk\IO\IStream::Open()} call will fail by returning FALSE and generating an error of level E_WARNING.
     * If the file does not exist, attempt to create it.
     */
    public const Create = 0b001000;
    
    /**
     * Binary mode. NT-Systems only.
     */
    public const Binary = 0b010000;
    
    /**
     * Additional constant expression for adding full "read and write"-functionality to every mode.
     */
    public const Duplex = 0b100000;
    
}

