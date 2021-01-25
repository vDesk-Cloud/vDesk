<?php
declare(strict_types=1);

namespace vDesk\IO;

use vDesk\IO\Stream\Lock;

/**
 * Provides an interface of a generic view of a sequence of bytes and file-based operations.
 *
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 */
interface IStream {
    
    /**
     * The default size of chunks to read or write. Equals the size of PHPs internal buffer.
     */
    public const DefaultChunkSize = 8192;
    
    /**
     * Initializes a new instance of the IStream class.
     *
     * @param string|null $Target Initializes the IStream with the specified target.
     */
    public function __construct(?string $Target);
    
    /**
     * Creates a new Stream as a wrapper for a specified resource.
     *
     * @param resource $Pointer The resource to use.
     *
     * @return \vDesk\IO\IStream A wrapper for the passed resource.
     */
    public static function FromPointer($Pointer): IStream;
    
    /**
     * Tells whether the current Stream has reached its end. EndOfStream is a convenience method that is equivalent to the value of the
     * EndOfStream property of the current instance.
     *
     * @return bool True if the Stream has reached its end; otherwise, false.
     */
    public function EndOfStream(): bool;
    
    /**
     * Opens the Stream on a specified target.
     *
     * @param string $Target The target file, protocol or socket.
     *
     * @return bool True on success, false on failure.
     */
    public function Open(string $Target): bool;
    
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