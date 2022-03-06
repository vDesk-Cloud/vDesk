<?php
declare(strict_types=1);

namespace vDesk\IO;

/**
 * Interface for seekable byte streams.
 *
 * @package vDesk
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
interface ISeekableStream extends IStream {

    /**
     * Tells whether the current ISeekableStream supports seeking.
     *
     * @return bool True if the ISeekableStream supports seeking; otherwise, false.
     */
    public function CanSeek(): bool;

    /**
     * Determines current position of the pointer of the ISeekableStream.
     *
     * @return null|int The current byte-offset of the pointer of the ISeekableStream or null if an error occurred.
     */
    public function Tell(): ?int;

    /**
     * Determines current position of the pointer of the ISeekableStream.
     *
     * @return null|int The current byte-offset of the pointer of the ISeekableStream or null if an error occurred.
     */
  //  public function Peek(): ?int;

    /**
     * Sets the current position of the pointer of the ISeekableStream to a specified offset.
     *
     * @param int $Offset The offset to set the pointer to.
     *
     * @return bool True on success; otherwise, false.
     */
    public function Seek(int $Offset): bool;

    /**
     * Resets the position of the internal pointer of the ISeekableStream.
     *
     * @return bool True on success, false on failure.
     */
    public function Rewind(): bool;
}