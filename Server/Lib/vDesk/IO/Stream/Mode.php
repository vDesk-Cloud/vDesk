<?php
declare(strict_types=1);

namespace vDesk\IO\Stream;

/**
 * Enumeration of file access modes of Streams.
 *
 * @package vDesk
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
abstract class Mode {

    /**
     * Flag for read access on Streams.
     */
    public const Read = 0b0000001;

    /**
     * Flag for write access on Streams.
     */
    public const Write = 0b0000010;

    /**
     * Flag for creating target files of Streams.
     */
    public const Create = 0b0000100;

    /**
     * Flag for creating and appending data to target files of Streams.
     */
    public const Append = 0b0001000;

    /**
     * Flag for creating and truncating target files of Streams.
     */
    public const Truncate = 0b0010000;

    /**
     * Flag for creating and overwriting target files of Streams.
     */
    public const Overwrite = 0b0100000;

    /**
     * Binary mode. NT-Systems only.
     */
    public const Binary = 0b1000000;

}