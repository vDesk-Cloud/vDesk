<?php
declare(strict_types=1);

namespace vDesk\IO;

use vDesk\IO\Stream\Lock;
use vDesk\IO\Stream\Mode;
use vDesk\IO\Stream\Readable;
use vDesk\IO\Stream\Seekable;
use vDesk\IO\Stream\Writable;
use vDesk\Struct\Properties;

/**
 * Provides a generic view of a sequence of bytes.
 *
 * @property-read resource $Pointer      Gets the underlying pointer of the FileStream.
 * @property bool          $Blocking     Gets or sets a flag indicating whether the FileStream is blocking.
 *
 * @package vDesk
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class FileStream implements IReadableStream, IWritableStream, ISeekableStream {

    use Properties, Readable, Writable, Seekable;

    /**
     * Initializes a new instance of the FileStream class.
     *
     * @param null|string $File     Initializes the FileStream with the specified target file to read or write.
     * @param int         $Mode     Initializes the FileStream with the specified access-mode.
     * @param bool        $Blocking Initializes the FileStream with the specified flag indicating whether any operations would block.
     *
     * @throws \vDesk\IO\IOException Thrown if the FileStream couldn't be opened on the specified file.
     */
    public function __construct(public ?string $File, public int $Mode = Mode::Read | Mode::Write | Mode::Binary, protected bool $Blocking = true) {
        $this->AddProperties([
            "Pointer"  => [\Get => fn() => $this->Pointer],
            "Blocking" => [
                \Get => fn(): bool => $this->Blocking,
                \Set => function(bool $Value): void {
                    $this->Blocking = $Value;
                    \stream_set_blocking($this->Pointer, $Value);
                }
            ]
        ]);

        if($File !== null) {
            $FileMode = "";

            //Transform file mode bitmask into "open()" compatible string.
            if(
                ($Mode & Mode::Write)
                && ($Mode & (Mode::Create | Mode::Append | Mode::Truncate | Mode::Overwrite))
            ) {
                //Special modes.
                if($Mode & Mode::Create) {
                    if(File::Exists($File)) {
                        throw new IOException("Can't open FileStream in create mode on file \"{$File}\"! File already exists.");
                    }
                    $FileMode = "x";
                } else if($Mode & Mode::Append) {
                    $FileMode = "a";
                } else if($Mode & Mode::Truncate) {
                    $FileMode = "w";
                } else if($Mode & Mode::Overwrite) {
                    $FileMode = "c";
                }
                if($Mode & Mode::Read) {
                    $FileMode .= "+";
                }
            } else if(($Mode & Mode::Read) || ($Mode & Mode::Write)) {
                //Basic read/write.
                if(!\str_starts_with($File, "php://") && !File::Exists($File)) {
                    throw new FileNotFoundException("Can't open FileStream on file \"{$File}\"! File doesn't exist.");
                }
                $FileMode = "r";
                if($Mode & Mode::Write) {
                    $FileMode .= "+";
                }
            } else {
                throw new \InvalidArgumentException("Unsupported file mode specified!");
            }
            if($Mode & Mode::Binary) {
                $FileMode .= "b";
            }

            $this->Pointer = @\fopen($File, $FileMode);
            if($this->Pointer === false) {
                throw new IOException("Can't open FileStream on file: \"{$File}\" with mode: \"{$FileMode}\"");
            }
        }
    }

    /**
     * Sets a lock on the FileStream, limiting or prohibiting access for other processes.
     *
     * @param int $Type The type of the lock. Either one value of {@link \vDesk\IO\Stream\Lock}.
     *
     * @return bool True on success, false on failure.
     */
    public function Lock(int $Type = Lock::Shared): bool {
        return \flock($this->Pointer, $Type);
    }

    /**
     * Unlocks the FileStream, granting access for other processes.
     *
     * @return bool True on success, false on failure.
     */
    public function Unlock(): bool {
        return \flock($this->Pointer, Lock::Free);
    }

    /** @inheritDoc */
    public function CanSeek(): bool {
        return !($this->Mode & Mode::Append);
    }

    /** @inheritDoc */
    public function CanRead(): bool {
        return (bool)($this->Mode & Mode::Read);
    }

    /** @inheritDoc */
    public function CanWrite(): bool {
        return (bool)($this->Mode & Mode::Write);
    }

    /** @inheritDoc */
    public static function FromPointer($Pointer, int $Mode, bool $Blocking = true): static {
        if(!\is_resource($Pointer)) {
            throw new \TypeError("Argument 1 passed to " . __METHOD__ . " must be of the type resource, " . \gettype($Pointer) . " given");
        }
        $Stream          = new static(null, $Mode);
        $Stream->Pointer = $Pointer;
        return $Stream;
    }
}
