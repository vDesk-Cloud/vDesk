<?php
declare(strict_types=1);

namespace vDesk\IO\Stream;

use vDesk\IO\IOException;
use vDesk\IO\Stream;

/**
 * Trait containing functionality for writable IStreams.
 */
trait Writable {

    use Stream;

    /**
     * Writes data to the Stream.
     *
     * @param string $Data The data to write.
     *
     * @return int The amount of bytes written.
     * @throws \vDesk\IO\IOException Thrown if the Stream doesn't support write-operations.
     */
    public function Write(string $Data, ?int $Amount = null): int {
        if(!$this->CanWrite()) {
            throw new IOException("Cannot write to readonly Stream!");
        }
        if($Amount === null) {
            return (int)\fwrite($this->Pointer, $Data);
        }
        return (int)\fwrite($this->Pointer, $Data, $Amount);
    }

    /**
     * Truncates the Stream to the specified size.
     *
     * @param int $Size The size to truncate to.
     *
     * @return bool True on success; otherwise, false.
     * @throws \vDesk\IO\IOException Thrown if the Stream doesn't support write-operations.
     */
    public function Truncate(int $Size): bool {
        if(!$this->CanWrite()) {
            throw new IOException("Cannot truncate readonly Stream!");
        }
        return \ftruncate($this->Pointer, $Size);
    }

}