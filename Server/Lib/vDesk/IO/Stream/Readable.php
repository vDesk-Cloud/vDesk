<?php
declare(strict_types=1);

namespace vDesk\IO\Stream;

use vDesk\IO\EndOfStreamException;
use vDesk\IO\IOException;
use vDesk\IO\IStream;
use vDesk\IO\Stream;

/**
 * Trait containing functionality for readable IStreams.
 */
trait Readable {

    use Stream;

    /**
     * Reads a given amount of bytes from the Stream.
     *
     * @param int $Amount The amount of bytes to read.
     *
     * @return null|string The read amount of bytes or null if an error occurred.
     * @throws \vDesk\IO\EndOfStreamException Thrown if the Stream has reached its end.
     * @throws \vDesk\IO\IOException Thrown if the Stream doesn't support read-operations.
     */
    public function Read(int $Amount = IStream::DefaultChunkSize): ?string {
        if(!$this->CanRead()) {
            throw new IOException("Cannot read from write-only Stream!");
        }
        if($this->EndOfStream()) {
            throw new EndOfStreamException("Can not read from end of Stream.");
        }
        return \fread($this->Pointer, $Amount) ?: null;
    }

    /**
     * Reads a line from the Stream.
     *
     * @return null|string The read line or null if an error occurred.
     * @throws \vDesk\IO\EndOfStreamException Thrown if the Stream has reached its end.
     * @throws \vDesk\IO\IOException Thrown if the Stream doesn't support read-operations.
     */
    public function ReadLine(): ?string {
        if(!$this->CanRead()) {
            throw new IOException("Cannot read from write-only Stream!");
        }
        if($this->EndOfStream()) {
            throw new EndOfStreamException("Can not read from end of Stream.");
        }
        return \fgets($this->Pointer) ?: null;
    }

    /**
     * Reads the entire content of the Stream from the current position until the end of the Stream.
     *
     * @param int $Amount The amount of bytes to read. If $Amount is set to -1, the entire content is read until the end of the Stream.
     * @param int $Offset The offset to start reading from. If $Offset is set to -1, reading starts from the current position of the
     *                    Stream.
     *
     * @return null|string The read content or null if an error occurred.
     * @throws \vDesk\IO\EndOfStreamException Thrown if the Stream has reached its end.
     * @throws \vDesk\IO\IOException Thrown if the Stream doesn't support read-operations.
     */
    public function ReadAll(int $Amount = -1, int $Offset = -1): ?string {
        if(!$this->CanRead()) {
            throw new IOException("Cannot read from write-only Stream!");
        }
        if($this->EndOfStream()) {
            throw new EndOfStreamException("Can not read from end of Stream.");
        }
        return \stream_get_contents($this->Pointer, $Amount, $Offset) ?: null;
    }

    /**
     * Reads a single character from the Stream.
     *
     * @return null|string The read character or null if an error occurred.
     * @throws \vDesk\IO\EndOfStreamException Thrown if the Stream has reached its end.
     * @throws \vDesk\IO\IOException Thrown if the Stream doesn't support read-operations.
     */
    public function ReadCharacter(): ?string {
        if(!$this->CanRead()) {
            throw new IOException("Cannot read from write-only Stream!");
        }
        if($this->EndOfStream()) {
            throw new EndOfStreamException("Can not read from end of Stream.");
        }
        return \fgetc($this->Pointer) ?: null;
    }
}