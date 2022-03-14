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
     * Tells whether the current Stream supports reading.
     *
     * @return bool True if the Stream supports reading; otherwise, false.
     */
    public function CanRead(): bool;

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

}