<?php
declare(strict_types=1);

namespace vDesk\IO;

/**
 * Interface for writable byte streams.
 *
 * @package vDesk
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
interface IWritableStream extends IStream {

    /**
     * Tells whether the current Stream supports writing.
     *
     * @return bool True if the Stream supports writing; otherwise, false.
     */
    public function CanWrite(): bool;

    /**
     * Writes data to the Stream.
     *
     * @param string $Data The data to write.
     *
     * @return int The amount of bytes written.
     */
    public function Write(string $Data): int;

    /**
     * Truncates the sequence of bytes to the specified size.
     *
     * @param int $Size The size to truncate to.
     *
     * @return bool True on success; otherwise, false.
     */
    public function Truncate(int $Size): bool;
}