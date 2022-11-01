<?php
declare(strict_types=1);

namespace vDesk\IO;

/**
 * Provides an interface of a generic view of a sequence of bytes and file-based operations.
 *
 * @package vDesk
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
interface IStream {

    /**
     * The default size of chunks to read or write. Equals the size of PHPs internal buffer.
     */
    public const DefaultChunkSize = 8192;

    /**
     * Initializes a new instance of the IStream class.
     *
     * @param null|string $Target   Initializes the IStream with the specified target.
     * @param int         $Mode     Initializes the IStream with the specified mode.
     * @param bool        $Blocking Initializes the IStream with the specified flag indicating whether any operations would block.
     */
    public function __construct(?string $Target, int $Mode, bool $Blocking = true);

    /**
     * Tells whether the current IStream has reached its end. EndOfStream is a convenience method that is equivalent to the value of the
     * EndOfStream property of the current instance.
     *
     * @return bool True if the Stream has reached its end; otherwise, false.
     */
    public function EndOfStream(): bool;

    /**
     * Factory method that creates a new instance of the IStream class wrapping a specified pointer.
     *
     * @param resource $Pointer  The pointer to create the IStream from.
     * @param int      $Mode     Initializes the created IStream with the specified mode.
     * @param bool     $Blocking Initializes the IStream with the specified flag indicating whether any operations would block.
     *
     * @return static A new instance of the IStream class wrapping the specified pointer.
     */
    public static function FromPointer($Pointer, int $Mode, bool $Blocking = true): static;

    /**
     * Frees any resources occupied by the IStream.
     */
    public function __destruct();
}