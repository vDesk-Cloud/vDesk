<?php
declare(strict_types=1);

namespace vDesk\IO;

use vDesk\IO\Stream\Lock;

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
     * @param string $Target Initializes the IStream with the specified target.
     */
    public function __construct(string $Target);

    /**
     * Tells whether the current Stream has reached its end. EndOfStream is a convenience method that is equivalent to the value of the
     * EndOfStream property of the current instance.
     *
     * @return bool True if the Stream has reached its end; otherwise, false.
     */
    public function EndOfStream(): bool;

    /**
     * Sets a lock on the Stream, limiting or prohibiting access for other processes.
     *
     * @param int $Type The type of the lock. Either one value of {@link \vDesk\IO\Stream\Lock}.
     *
     * @return bool True on success, false on failure.
     */
    public function Lock(int $Type = Lock::Shared): bool;

    /**
     * Unlocks the Stream, granting access for other processes.
     *
     * @return bool True on success, false on failure.
     */
    public function Unlock(): bool;

    /**
     * Closes the Stream.
     *
     * @return bool True on success, false on failure.
     */
    public function Close(): bool;
}