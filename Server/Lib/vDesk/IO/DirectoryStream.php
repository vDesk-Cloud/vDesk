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
 * @package vDesk
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class DirectoryStream implements IReadableStream, ISeekableStream {

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
     *
     * @var resource
     */
    private $Pointer;

    /**
     * Initializes a new instance of the DirectoryStream class.
     *
     * @param null|string $Directory Initializes the DirectoryStream with the specified target directory to iterate over.
     *
     * @throws \vDesk\IO\IOException
     */
    public function __construct(public ?string $Directory, protected int $Mode = 0, protected bool $Blocking = true) {
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
                \Get => fn() => $this->Pointer,
                \Set => fn($Value) => $this->Pointer = $Value
            ]
        ]);
        $this->Pointer = @\opendir($Directory);
        if($this->Pointer === false) {
            if(!Directory::Exists($Directory)) {
                throw new IOException("Can't open DirectoryStream on directory: \"{$Directory}\", directory doesn't exist!");
            }
            if(!Directory::IsReadable($Directory)) {
                throw new IOException("Can't open DirectoryStream on directory: \"{$Directory}\", access denied!");
            }
            throw new IOException("Can't open DirectoryStream on directory: \"{$Directory}\", filesystem made shit?");
        }
    }

    /** @inheritDoc */
    public function EndOfStream(): bool {
        if($this->EndOfStream || !$this->Pointer) {
            return $this->EndOfStream;
        }
        if($this->Entry === null) {
            $Element = \readdir($this->Pointer);
            if($Element === "." || $Element === "..") {
                $Element = \readdir($this->Pointer);
                if($Element === "." || $Element === "..") {
                    $Element = \readdir($this->Pointer);
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

    /** @inheritDoc */
    public function CanRead(): bool {
        return true;
    }

    /** @inheritDoc */
    public function Read(int $Amount = IStream::DefaultChunkSize): string {
        if($this->Entry !== null && $this->Entry !== "." && $this->Entry !== "..") {
            $Element     = $this->Entry;
            $this->Entry = null;
            return $Element;
        }
        if($this->Pointer && ($Element = \readdir($this->Pointer)) !== false) {
            $this->Entry = null;
            if($Element === "." || $Element === "..") {
                return $this->Read();
            }
            return $Element;
        }
        $this->EndOfStream = true;
        return Text::Empty;
    }

    /**
     * Reads a single character from the Stream.
     *
     * @return string The read character.
     */
    public function ReadCharacter(): string {
        return $this->Read();
    }

    /**
     * Reads a line from the Stream.
     *
     * @return string The read line.
     */
    public function ReadLine(): string {
        return $this->Read();
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

    /** @inheritDoc */
    public function CanSeek(): bool {
        return true;
    }

    /** @inheritDoc */
    public function Tell(): int {
        return \ftell($this->Pointer);
    }

    /** @inheritDoc */
    public function Seek(int $Offset): bool {
        \fseek($this->Pointer, $Offset);
        return true;
    }

    /** @inheritDoc */
    public function Rewind(): bool {
        \rewinddir($this->Pointer);
        $this->Entry       = null;
        $this->EndOfStream = false;
        return true;
    }

    /**
     * Closes the DirectoryStream.
     */
    public function __destruct() {
        if(\is_resource($this->Pointer)) {
            \closedir($this->Pointer);
        }
    }

    public static function FromPointer($Pointer, int $Mode, bool $Blocking = true): static {
        // TODO: Implement FromPointer() method.
    }
    public function Close(): bool {
        return true;
    }
}

