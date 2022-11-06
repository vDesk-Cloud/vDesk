<?php
declare(strict_types=1);

namespace vDesk\IO;

/**
 * Interface for readable byte streams.
 *
 * @package vDesk
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
interface IReadableStream extends IStream {

    /**
     * Tells whether the current IReadableStream supports reading.
     *
     * @return bool True if the IReadableStream supports reading; otherwise, false.
     */
    public function CanRead(): bool;

    /**
     * Reads a given amount of bytes from the IReadableStream.
     *
     * @param int $Amount The amount of bytes to read.
     *
     * @return null|string The read amount of bytes or null if an error occurred.
     */
    public function Read(int $Amount = IStream::DefaultChunkSize): ?string;

    /**
     * Reads a line from the IReadableStream.
     *
     * @return null|string The read line or null if an error occurred.
     */
    public function ReadLine(): ?string;

    /**
     * Reads the entire content of the IReadableStream from the current position until the end of the IReadableStream.
     *
     * @param int $Amount The amount of bytes to read. If $Amount is set to -1, the entire content is read until the end of the IReadableStream.
     * @param int $Offset The offset to start reading from. If $Offset is set to -1, reading starts from the current position of the
     *                    IReadableStream.
     *
     * @return null|string The read content or null if an error occurred.
     */
    public function ReadAll(int $Amount = -1, int $Offset = -1): ?string;

    /**
     * Reads a single character from the IReadableStream.
     *
     * @return null|string The read character or null if an error occurred.
     */
    public function ReadCharacter(): ?string;
}