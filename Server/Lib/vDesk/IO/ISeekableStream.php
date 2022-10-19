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
     * Tells whether the current Stream supports seeking.
     *
     * @return bool True if the Stream supports seeking; otherwise, false.
     */
    public function CanSeek(): bool;

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
     * Resets the position of the internal pointer of the Stream.
     *
     * @return bool True on success, false on failure.
     */
    public function Rewind(): bool;

}