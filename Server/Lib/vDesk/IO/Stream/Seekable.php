<?php
declare(strict_types=1);

namespace vDesk\IO\Stream;

use vDesk\IO\IOException;
use vDesk\IO\Stream;

/**
 * Trait containing functionality for seekable IStreams.
 *
 * @package vDesk
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
trait Seekable {

    use Stream;

    /**
     * Sets the current position of the pointer of the Stream to a specified offset.
     *
     * @param int $Offset The offset to set the pointer to.
     *
     * @return bool True on success; otherwise, false.
     * @throws \vDesk\IO\IOException Thrown if the Stream doesn't support seek-operations.
     */
    public function Seek(int $Offset): bool {
        if(!$this->CanSeek()) {
            throw new IOException("Cannot seek on non-seekable Stream!");
        }
        return \fseek($this->Pointer, $Offset) > -1;
    }

    /**
     * Resets the position of the internal pointer of the Stream.
     *
     * @return bool True on success, false on failure.
     * @throws \vDesk\IO\IOException Thrown if the Stream doesn't support seek-operations.
     */
    public function Rewind(): bool {
        if(!$this->CanSeek()) {
            throw new IOException("Cannot seek on non-seekable Stream!");
        }
        return \rewind($this->Pointer);
    }

    /**
     * Determines current position of the pointer of the Stream.
     *
     * @return null|int The current byte-offset of the pointer of the ISeekableStream or null if an error occurred.
     * @throws \vDesk\IO\IOException Thrown if the Stream doesn't support seek-operations.
     */
    public function Tell(): ?int {
        if(!$this->CanSeek()) {
            throw new IOException("Cannot seek on non-seekable Stream!");
        }
        return \ftell($this->Pointer) ?: null;
    }
}