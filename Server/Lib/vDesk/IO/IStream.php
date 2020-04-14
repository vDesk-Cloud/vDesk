<?php
declare(strict_types=1);

namespace vDesk\IO;

use vDesk\IO\Stream\Lock;

/**
 * Provides an interface of a generic view of a sequence of bytes and file-based operations.
 *
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 * @version 1.0.0.
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
     * @param int|null    $Mode Initializes the IStream with the specified mode.
     */
    public function __construct(?string $Target, ?int $Mode = null);

    /**
     * Creates a new Stream as a wrapper for a specified resource.
     *
     * @param resource $Handle The resource to use.
     *
     * @return \vDesk\IO\IStream A wrapper for the passed resource.
     */
    public static function FromHandle($Handle): IStream;

    /**
     * Tells whether the current Stream supports seeking. CanSeek is a convenience method that is equivalent to the value of the CanSeek
     * property of the current instance.
     *
     * @return bool True if the Stream supports seeking; otherwise, false.
     */
    public function CanSeek(): bool;

    /**
     * Tells whether the current Stream supports reading. CanRead is a convenience method that is equivalent to the value of the CanRead
     * property of the current instance.
     *
     * @return bool True if the Stream supports reading; otherwise, false.
     */
    public function CanRead(): bool;

    /**
     * Tells whether the current Stream supports writing. CanWrite is a convenience method that is equivalent to the value of the CanWrite
     * property of the current instance.
     *
     * @return bool True if the Stream supports writing; otherwise, false.
     */
    public function CanWrite(): bool;

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
     * Reads a given amount of bytes from the Stream.
     *
     * @param int $Amount The amount of bytes to read.
     *
     * @return string The read amount of bytes.
     */
    public function Read(int $Amount = IStream::DefaultChunkSize): string;

    /**
     * Reads a line from the Stream.
     *
     * @return string The read line.
     */
    public function ReadLine(): string;

    /**
     * Reads the entire content of the Stream from the current position until the end of the Stream.
     *
     * @param int $Amount The amount of bytes to read. If $Amount is set to -1, the entire content is read until the end of the Stream.
     * @param int $Offset The offset to start reading from. If $Offset is set to -1, reading starts from the current position of the
     *                    Stream.
     *
     * @return mixed The read content.
     */
    public function ReadAll(int $Amount = -1, int $Offset = -1);

    /**
     * Reads a single character from the Stream.
     *
     * @return string The read character.
     */
    public function ReadCharacter(): string;

    /**
     * Writes data to the Stream.
     *
     * @param string $Data The data to write.
     *
     * @return int The amount of bytes written.
     */
    public function Write(string $Data): int;

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
     * Resets the position of the internal pointer of the Stream.
     *
     * @return bool True on success, false on failure.
     */
    public function Rewind(): bool;

    /**
     * Determines current position of the pointer of the Stream.
     *
     * @return int The current byte-offset of the pointer of the Stream.
     */
    public function Tell(): int;

    /**
     * Sets the current position of the pointer of the Stream to a specified offset.
     *
     * @param int $Offset The offset to set the pointer to.
     *
     * @return bool True on success; otherwise, false.
     */
    public function Seek(int $Offset): bool;

    /**
     * Closes the Stream.
     *
     * @return bool True on success, false on failure.
     */
    public function Close(): bool;

    /**
     * Truncates the sequence of bytes to the specified size.
     *
     * @param int $Size The size to truncate to.
     *
     * @return bool True on success; otherwise, false.
     */
    public function Truncate(int $Size): bool;

}