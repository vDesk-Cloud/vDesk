<?php
declare(strict_types=1);

namespace vDesk\IO;

/**
 * Enumeration of specifications how the operating system should open a file.
 *
 * @package vDesk\IO
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 * @todo Make this bitset.
 */
abstract class FileMode {

    /**
     * Open for reading only; place the file pointer at the beginning of the file.
     */
    public const Open = "r";

    /**
     * Open for reading and writing; place the file pointer at the beginning of the file.
     */
    public const OpenRW = self::Open . self::Write;

    /**
     * Opens the file if it exists and seeks to the end of the file, or creates a new file.
     * This requires FileIOPermissionAccess.Append permission.
     * FileMode.Append can be used only in conjunction with FileAccess.Write.
     * Trying to seek to a position before the end of the file throws an IOException exception, and any attempt to read fails and throws a
     * NotSupportedException exception.
     */
    public const Append = "a";

    /**
     * Create and open for writing only; place the file pointer at the beginning of the file.
     */
    public const Create = "x";

    /**
     * Create and open for writing only; place the file pointer at the beginning of the file.
     */
    public const CreateRW = self::Create . self::Write;

    /**
     *
     */
    public const OverwriteRead = "w";

    /**
     *
     */
    public const OverwriteReadWrite = "w+";

    /**
     *
     */
    public const Truncate = "b";

    /**
     *
     */
    public const Binary = "b";

    /**
     *
     */
    public const Write = "+";

}