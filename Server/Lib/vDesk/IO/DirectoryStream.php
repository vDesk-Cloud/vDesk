<?php
declare(strict_types=1);

namespace vDesk\IO;

use vDesk\IO\Stream\Lock;
use vDesk\Struct\Properties;
use vDesk\Struct\Text;

/**
 * Represents a stream for sequentially reading contents of a directory.
 *
 * @property-read bool     $CanSeek     Gets a value indicating whether the current DirectoryStream supports seeking.
 * @property-read bool     $CanRead     Gets a value indicating whether the current DirectoryStream supports reading.
 * @property-read bool     $CanWrite    Gets a value indicating whether the current DirectoryStream supports writing.
 * @property-read resource $Handle      Gets the operating system handle of the DirectoryStream.
 * @property-read bool     $EndOfStream Gets a value indicating whether the current DirectoryStream has reached its end.
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
class DirectoryStream implements IStream {

    use Properties;

    /**
     * Gets a value indicating whether the current DirectoryStream has reached its end.
     *
     * @var bool
     */
    public bool $EndOfStream = false;

    /**
     * The last read entry of the DirectoryStream.
     *
     * @var string|null
     */
    private ?string $Entry = null;

    /**
     * The handle of the DirectoryStream.
     * @var resource
     */
    private $Handle;

    /**
     * Initializes a new instance of the DirectoryStream class.
     *
     * @param string   $Directory The target directory to read.
     * @param int|null $Mode
     */
    public function __construct(?string $Directory = null, ?int $Mode = null) {
        $this->AddProperties([
            "CanSeek"     => [
                \Get => fn(): bool => $this->CanSeek()
            ],
            "CanRead"     => [
                \Get => fn(): bool => $this->CanRead()
            ],
            "CanWrite"    => [
                \Get => fn(): bool => $this->CanWrite()
            ],
            "Position"    => [
                \Get => fn(): int => $this->Tell()
            ],
            "EndOfStream" => [
                \Get => fn(): bool => $this->EndOfStream()
            ],
            "Handle"      => [
                \Get => fn() => $this->Handle,
                \Set => fn($Value) => $this->Handle = $Value
            ]
        ]);
        if($Directory !== null && Directory::Exists($Directory)) {
            $this->Open($Directory);
        }
    }

    /**
     * Opens a Stream on a given target directory.
     *
     * @param string $Directory The target directory to read.
     *
     * @return bool True on success, false on failure.
     */
    public function Open(string $Directory): bool {
        return !($this->Handle !== null || ($this->Handle = @\opendir($Directory)) === false);
    }

    /**
     * Reads an entry from the DirectoryStream.
     *
     * @param int $Amount This parameter doesn't have any effect.
     *
     * @return string The name of the next entry in the directory or @see DirectoryStream::EndOfDirectory if the end of the DirectoryStream
     *                has been reached. The entries are returned in the order in which they are stored by the filesystem.
     */
    public function Read(int $Amount = IStream::DefaultChunkSize): string {
        if($this->Entry !== null && $this->Entry !== "." && $this->Entry !== "..") {
            $Element     = $this->Entry;
            $this->Entry = null;
            return $Element;
        }
        if($this->Handle && ($Element = \readdir($this->Handle)) !== false) {
            if($Element === "." || $Element === "..") {
                $this->Entry = null;
                return $this->Read();
            }
            $this->Entry = null;
            return $Element;
        }
        $this->EndOfStream = true;
        return Text::Empty;
    }

    /**
     * Resets the position of the internal pointer of the DirectoryStream.
     *
     * @return bool True on success, false on failure.
     */
    public function Rewind(): bool {
        \rewinddir($this->Handle);
        $this->Entry       = null;
        $this->EndOfStream = false;
        return true;
    }

    /**
     * Tells whether the current DirectoryStream supports seeking.
     * CanSeek is a convenience method that is equivalent to the value of the CanSeek property of the current instance.
     *
     * @return bool This method always returns false.
     */
    public function CanSeek(): bool {
        return false;
    }

    /**
     * Tells whether the current DirectoryStream supports reading.
     * CanRead is a convenience method that is equivalent to the value of the CanRead property of the current instance.
     *
     * @return bool This method always returns true.
     */
    public function CanRead(): bool {
        return true;
    }

    /**
     * Tells whether the current DirectoryStream supports writing.
     * CanWrite is a convenience method that is equivalent to the value of the CanWrite property of the current instance.
     *
     * @return bool This method always returns false.
     */
    public function CanWrite(): bool {
        return false;
    }

    /**
     * Tells whether the current DirectoryStream has reached its end.
     * EndOfStream is a convenience method that is equivalent to the value of the EndOfStream property of the current instance.
     *
     * @return bool True if the DirectoryStream has reached its end; otherwise, false.
     */
    public function EndOfStream(): bool {
        if($this->EndOfStream || !$this->Handle) {
            return $this->EndOfStream;
        }
        if($this->Entry === null) {
            $Element = \readdir($this->Handle);
            if($Element === "." || $Element === "..") {
                $Element = \readdir($this->Handle);
                if($Element === "." || $Element === "..") {
                    $Element = \readdir($this->Handle);
                }
            }
            if($Element === false) {
                $this->EndOfStream = true;
            } else {
                $this->Entry       = $Element;
                $this->EndOfStream = false;
            }
        }
        return $this->EndOfStream;
    }

    /**
     * Closes the DirectoryStream.
     *
     * @return bool True on success; otherwise, false.
     */
    public function Close(): bool {
        if(\is_resource($this->Handle)) {
            \closedir($this->Handle);
            return true;
        }
        return false;
    }

    // Following methods are not supported by DirectoryStream.

    /**
     * Writes data to the Stream.
     *
     * @param string $Data The data to write.
     *
     * @return int The amount of bytes written.
     */
    public function Write(string $Data): int {
    }

    /**
     * Reads a single character from the Stream.
     *
     * @return string The read character.
     */
    public function ReadCharacter(): string {
    }

    /**
     * Unlocks the Stream, granting access for other processes.
     *
     * @return bool True on success, false on failure.
     */
    public function Unlock(): bool {
    }

    /**
     * Truncates the sequence of bytes to the specified size.
     *
     * @param int $Size The size to truncate to.
     *
     * @return bool True on success; otherwise, false.
     */
    public function Truncate(int $Size): bool {
    }

    /**
     * Reads a line from the Stream.
     *
     * @return string The read line.
     */
    public function ReadLine(): string {
    }

    /**
     * Determines current position of the pointer of the Stream.
     *
     * @return int The current byte-offset of the pointer of the Stream.
     */
    public function Tell(): int {
    }

    /**
     * Sets the current position of the pointer of the Stream to a specified offset.
     *
     * @param int $Offset The offset to set the pointer to.
     *
     * @return bool True on success; otherwise, false.
     */
    public function Seek(int $Offset): bool {
    }

    /**
     * Reads the entire content of the Stream from the current position until the end of the Stream.
     *
     * @param int $Amount The amount of bytes to read. If $Amount is set to -1, the entire content is read until the end of the Stream.
     * @param int $Offset The offset to start reading from. If $Offset is set to -1, reading starts from the current position of the
     *                    Stream.
     *
     * @return string The read content.
     */
    public function ReadAll(int $Amount = -1, int $Offset = -1): string {
        // TODO: Implement ReadAll() method.
    }

    /**
     * Sets a lock on the Stream, limiting or prohibiting access for other processes.
     *
     * @param int $Type The type of the lock. Either one value of {@link \vDesk\IO\Stream\Lock}.
     *
     * @return bool True on success, false on failure.
     */
    public function Lock(int $Type = Lock::Shared): bool {
        // TODO: Implement Lock() method.
    }

    /**
     * @inheritDoc
     */
    public static function FromHandle($Handle): IStream {
        // TODO: Implement FromHandle() method.
    }
}

