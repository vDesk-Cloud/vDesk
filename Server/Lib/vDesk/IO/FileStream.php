<?php
declare(strict_types=1);

namespace vDesk\IO;

use vDesk\IO\Stream\Lock;
use vDesk\IO\Stream\Mode;
use vDesk\IO\Stream\Seek;
use vDesk\Struct\Properties;

/**
 * Provides a generic view of a sequence of bytes.
 *
 * @property-read resource $Pointer     Gets the underlying pointer of the FileStream.
 * @property-read bool     $CanSeek     Gets a value indicating whether the current FileStream supports seeking.
 * @property-read bool     $CanRead     Gets a value indicating whether the current FileStream supports reading.
 * @property-read bool     $CanWrite    Gets a value indicating whether the current FileStream supports writing.
 * @property-read int      $Position    Gets the current position of the pointer of the FileStream.
 * @property-read bool     $EndOfStream Gets a value indicating whether the current FileStream has reached its end.
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 */
class FileStream implements IReadableStream, IWritableStream, ISeekableStream {
    
    use Properties;
    
    /**
     * The access mode of the FileStream.
     *
     * @var int|null
     */
    protected ?int $Mode;
    
    /**
     * The pointer of the FileStream.
     *
     * @var resource
     */
    protected $Pointer;
    
    /**
     * The target file of the FileStream.
     *
     * @var null|string
     */
    public ?string $File;
    
    /**
     * Initializes a new instance of the FileStream class.
     *
     * @param null|string $File The target file to read or write.
     * @param int|null    $Mode The access-mode on the FileStream.
     */
    public function __construct(?string $File = null, int $Mode = null) {
        $this->AddProperty("Pointer", [\Get => fn() => $this->Pointer]);
        if($File !== null) {
            $this->Open($File, $Mode ?? Mode::Read | Mode::Duplex | Mode::Binary);
        }
    }
    
    /**
     * Opens a Stream on a given target file or creates a new file if it does not exist.
     *
     * @param string $File The target file to read or write.
     * @param int    $Mode The access-mode on the FileStream.
     *
     * @return bool True on success, false on failure.
     */
    public function Open(string $File, int $Mode = Mode::Read | Mode::Duplex | Mode::Binary): bool {
        $FileMode = "";
        if($Mode & Mode::Read) {
            $FileMode .= "r";
        }
        if($Mode & Mode::Truncate) {
            $FileMode .= "w";
        }
        if($Mode & Mode::Append) {
            $FileMode .= "a";
        }
        if($Mode & Mode::Read && $Mode & Mode::Create) {
            $FileMode .= "c";
        }
        if($Mode & Mode::Create) {
            $FileMode .= "x";
        }
        if($Mode & Mode::Binary) {
            $FileMode .= "b";
        }
        if($Mode & Mode::Duplex) {
            $FileMode .= "+";
        }
        if($this->Pointer !== null || ($this->Pointer = @\fopen($File, $FileMode)) === false) {
            return false;
        }
        $this->File = $File;
        $this->Mode = $Mode;
        return true;
    }
    
    /**
     * Reads a given amount of bytes from the FileStream.
     *
     * @param int $Amount The amount of bytes to read.
     *
     * @return string The read amount of bytes.
     * @throws EndOfStreamException Thrown if the Stream has reached its end.
     */
    public function Read(int $Amount = IStream::DefaultChunkSize): string {
        if($this->EndOfStream()) {
            throw new EndOfStreamException("Can not read from end of Stream.");
        }
        return \fread($this->Pointer, $Amount);
    }
    
    /**
     * Reads a line from the FileStream.
     *
     * @return string The read line.
     * @throws EndOfStreamException Thrown if the Stream has reached its end.
     */
    public function ReadLine(): string {
        if($this->EndOfStream()) {
            throw new EndOfStreamException("Can not read from end of Stream.");
        }
        return \fgets($this->Pointer);
    }
    
    /**
     * Reads the entire content of the FileStream from the current position until the end of the FileStream.
     *
     * @param int $Amount The amount of bytes to read. If $Amount is set to -1, the entire content is read until the end of the FileStream.
     * @param int $Offset The offset to start reading from. If $Offset is set to -1, reading starts from the current position of the
     *                    FileStream.
     *
     * @return string The read content.
     * @throws EndOfStreamException Thrown if the Stream has reached its end.
     */
    public function ReadAll(int $Amount = -1, int $Offset = -1): string {
        if($this->EndOfStream()) {
            throw new EndOfStreamException("Can not read from end of Stream.");
        }
        return \stream_get_contents($this->Pointer, $Amount, $Offset);
    }
    
    /**
     * Reads a single character from the FileStream.
     *
     * @return string The read character.
     * @throws EndOfStreamException Thrown if the Stream has reached its end.
     */
    public function ReadCharacter(): string {
        if($this->EndOfStream()) {
            throw new EndOfStreamException("Can not read from end of Stream.");
        }
        return \fgetc($this->Pointer);
    }
    
    /**
     * Writes data to the FileStream.
     *
     * @param string   $Data   The data to write.
     * @param null|int $Amount The amount of bytes to write.
     *
     * @return int The amount of bytes written.
     */
    public function Write(string $Data, ?int $Amount = null): int {
        if($Amount === null) {
            return \fwrite($this->Pointer, $Data);
        }
        return \fwrite($this->Pointer, $Data, $Amount);
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
    
    /**
     * Resets the position of the internal pointer of the FileStream.
     *
     * @return bool True on success, false on failure.
     */
    public function Rewind(): bool {
        return \rewind($this->Pointer);
    }
    
    /**
     * Determines current position of the pointer of the FileStream.
     *
     * @return int The current byte-offset of the pointer of the FileStream.
     */
    public function Tell(): int {
        return \ftell($this->Pointer);
    }
    
    /**
     * Sets the current position of the pointer of the FileStream to a specified offset.
     *
     * @param int $Offset The offset to set the pointer to.
     * @param int $Mode   The mode where the offset begins.
     *
     * @return bool True on success; otherwise, false.
     */
    public function Seek(int $Offset, int $Mode = Seek::Offset): bool {
        return \fseek($this->Pointer, $Offset, $Mode) > -1;
    }
    
    /**
     * Tells whether the current FileStream supports seeking.
     * CanSeek is a convenience method that is equivalent to the value of the CanSeek property of the current instance.
     *
     * @return bool True if the FileStream supports seeking; otherwise, false.
     */
    public function CanSeek(): bool {
        return $this->Mode !== Mode::Append;
    }
    
    /**
     * Tells whether the current FileStream supports reading.
     * CanRead is a convenience method that is equivalent to the value of the CanRead property of the current instance.
     *
     * @return bool True if the FileStream supports reading; otherwise, false.
     */
    public function CanRead(): bool {
        return $this->Mode !== Mode::Truncate
               && $this->Mode !== Mode::Append
               && $this->Mode !== Mode::Create;
    }
    
    /**
     * Tells whether the current FileStream supports writing.
     * CanWrite is a convenience method that is equivalent to the value of the CanWrite property of the current instance.
     *
     * @return bool True if the FileStream supports writing; otherwise, false.
     */
    public function CanWrite(): bool {
        return $this->Mode !== Mode::Read;
    }
    
    /**
     * Tells whether the current FileStream has reached its end.
     * EndOfStream is a convenience method that is equivalent to the value of the EndOfStream property of the current instance.
     *
     * @return bool True if the FileStream has reached its end; otherwise, false.
     */
    public function EndOfStream(): bool {
        return \feof($this->Pointer);
    }
    
    /**
     * Closes the FileStream.
     *
     * @return bool True on success, false on failure.
     */
    public function Close(): bool {
        return \is_resource($this->Pointer) ? \fclose($this->Pointer) : false;
    }
    
    /**
     * Truncates the sequence of bytes to the specified size.
     *
     * @param int $Size The size to truncate to.
     *
     * @return bool True on success; otherwise, false.
     */
    public function Truncate(int $Size): bool {
        return \ftruncate($this->Pointer, $Size);
    }
    
    /**
     * Creates a new FileStream as wrapper of a specified pointer resource.
     *
     * @param resource $Pointer The pointer to wrap.
     *
     * @return \vDesk\IO\FileStream A new instance of the FileStream yielding the specified pointer.
     */
    public static function FromPointer($Pointer): IStream {
        $Stream          = new static();
        $Stream->Pointer = $Pointer;
        return $Stream;
    }
}
