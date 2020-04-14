<?php
declare(strict_types=1);

namespace vDesk\IO;

use vDesk\Struct\Properties;
use vDesk\Struct\Text;
use vDesk\Struct\Text\Encoding;

/**
 * Reads primitive data types as binary values in a specific encoding.
 *
 * @property \vDesk\IO\IStream Stream   Gets the input Stream of the BinaryReader.
 * @property string Encoding Gets or sets the encoding of the BinaryReader.
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
class BinaryReader {

    use Properties;

    /**
     * The supported encodings of the BinaryReader.
     */
    public const Encodings = [
        Encoding::ASCII,
        Encoding::UTF8
    ];

    /**
     * Unsigned 8-bit integer.
     */
    public const UInt8 = 0;

    /**
     * Signed 8-bit integer.
     */
    public const Int8 = 1;

    /**
     * Unsigned 16-bit integer.
     */
    public const UInt16 = 2;

    /**
     * Signed 16-bit integer.
     */
    public const Int16 = 3;

    /**
     * Unsigned 32-bit integer.
     */
    public const UInt32 = 4;

    /**
     * Signed 32-bit integer.
     */
    public const Int32 = 5;

    /**
     * Unsigned 64-bit integer.
     */
    public const UInt64 = 6;

    /**
     * Signed 64-bit integer.
     */
    public const Int64 = 7;

    /**
     * Unsigned 32-bit floating point number.
     */
    public const Float = 8;

    /**
     * Unsigned 64-bit floating point number.
     */
    public const Double = 9;

    /**
     * Boolean.
     */
    public const Boolean = 10;

    /**
     * The current Stream of the BinaryReader.
     *
     * @var \vDesk\IO\IStream|null
     */
    protected ?IStream $Stream;

    /**
     * The current encoding of the BinaryReader.
     *
     * @var string
     */
    protected string $Encoding;

    /**
     * Initializes a new instance of the BinaryReader class based on the specified stream and character encoding.
     *
     * @param \vDesk\IO\IStream $Stream   The input stream.
     * @param string $Encoding The character encoding to use.
     *
     * @throws \InvalidArgumentException Thrown if the specified encoding is not supported.
     */
    public function __construct(IStream $Stream, $Encoding = Encoding::UTF8) {
        if(!$Stream->CanRead()) {
            throw new \InvalidArgumentException("The provided Stream doesn't support 'read'-operations.");
        }
        if(!\in_array($Encoding, static::Encodings)) {
            throw new \InvalidArgumentException("'$Encoding' is not a supported encoding.");
        }
        $this->Stream   = $Stream;
        $this->Encoding = $Encoding;
        $this->AddProperties([
            "Stream"   => [
                \Get => fn(): IStream => $this->Stream
            ],
            "Encoding" => [
                \Get => fn(): string => $this->Encoding,
                \Set => function(string $Value): void {
                    if(!\in_array($Value, static::Encodings)) {
                        throw new \InvalidArgumentException("'$Value' is not a supported encoding.");
                    }
                    $this->Encoding = $Value;
                }
            ]
        ]);
    }

    /**
     * Reads a specified amount of values from the current Stream beginning at a specified offset
     * and advances the current position of the Stream by the byte-length of the amount of read values.
     *
     * @param int $Type   The type of the value to read.
     * @param int $Amount The amount of values to read.
     * @param int $Offset The offset where reading begins.
     *
     * @throws EndOfStreamException Thrown if the current Stream has reached its end.
     * @throws \InvalidArgumentException Thrown if the specified type is invalid.
     * @return array The read values from the current Stream.
     */
    public function Read(int $Type, int $Amount = 1, int $Offset = null): array {
        if($Offset !== null && $this->Stream->CanSeek()) {
            $this->Stream->Seek($Offset);
        }
        if($this->Stream->EndOfStream()) {
            throw new EndOfStreamException("Can not read from end of Stream.");
        }
        switch($Type) {
            case self::Int8:
                $Method = "ReadByte";
                break;
            case self::UInt8:
                $Method = "ReadSByte";
                break;
            case self::Int16:
                $Method = "ReadInt16";
                break;
            case self::UInt16:
                $Method = "ReadUInt16";
                break;
            case self::Int32:
                $Method = "ReadInt32";
                break;
            case self::UInt32:
                $Method = "ReadUInt32";
                break;
            case self::Int64:
                $Method = "ReadInt64";
                break;
            case self::UInt64:
                $Method = "ReadUInt64";
                break;
            case self::Float:
                $Method = "ReadFloat";
                break;
            case self::Double:
                $Method = "ReadDouble";
                break;
            case self::Boolean:
                $Method = "ReadBoolean";
                break;
            default:
                throw new \InvalidArgumentException("The specified type is invalid.");
                break;
        }

        $Values = [];
        for($i = 0; $i < $Amount && !$this->Stream->EndOfStream(); $i++) {
            $Values[] = $this->{$Method}();
        }
        return $Values;
    }

    /**
     * Reads a specified amount of bytes from the current Stream beginning at a specified offset
     * and advances the current position of the Stream by the specified amount of bytes.
     *
     * @param int $Amount The amount of bytes to read.
     * @param int $Offset The offset where reading begins.
     *
     * @throws EndOfStreamException Thrown if the current Stream has reached its end.
     * @return array The read bytes from the current Stream.
     */
    public function ReadBytes(int $Amount = 1, int $Offset = null): array {
        if($Offset !== null && $this->Stream->CanSeek()) {
            $this->Stream->Seek($Offset);
        }
        if($this->Stream->EndOfStream()) {
            throw new EndOfStreamException("Can not read from end of Stream.");
        }
        $Bytes = [];
        for($i = 0; $i < $Amount && !$this->Stream->EndOfStream(); $i++) {
            $Bytes[] = \ord($this->Stream->Read(1));
        }
        return $Bytes;
    }

    /**
     * Reads the next byte from the current Stream and advances the current position of the Stream by one byte.
     *
     * @throws EndOfStreamException Thrown if the current Stream has reached its end.
     * @return int The next byte read from the current Stream.
     */
    public function ReadByte(): int {
        if($this->Stream->EndOfStream()) {
            throw new EndOfStreamException("Can not read from end of Stream.");
        }
        return Text::IsNullOrEmpty($Char = $this->Stream->Read(1)) ? 0x00 : unpack("C", $Char)[1];
    }

    /**
     * Reads a signed byte from this Stream and advances the current position of the Stream by one byte.
     *
     * @throws EndOfStreamException Thrown if the current Stream has reached its end.
     * @return int A signed byte read from the current Stream.
     */
    public function ReadSByte(): int {
        if($this->Stream->EndOfStream()) {
            throw new EndOfStreamException("Can not read from end of Stream.");
        }
        return Text::IsNullOrEmpty($Char = $this->Stream->Read(1)) ? 0x00 : \unpack("c", $Char)[1];
    }

    /**
     * Reads a 1-byte signed integer from the current Stream and advances the current position of the Stream by one byte.
     * Alias of {@see BinaryReader::ReadSByte()}
     *
     * @return int A 1-byte signed integer read from the current Stream.
     */
    public function ReadInt8(): int {
        return $this->ReadSByte();
    }

    /**
     * Reads a 1-byte unsigned integer from the current Stream and advances the position of the Stream by one byte.
     * Alias of {@see BinaryReader::ReadByte()}
     *
     * @return int A 1-byte unsigned integer read from the current Stream.
     */
    public function ReadUInt8(): int {
        return $this->ReadByte();
    }

    /**
     * Reads a 2-byte signed integer from the current Stream and advances the current position of the Stream by two bytes.
     *
     * @throws EndOfStreamException Thrown if the current Stream has reached its end.
     * @return int A 2-byte signed integer read from the current Stream.
     */
    public function ReadInt16(): int {
        if($this->Stream->EndOfStream()) {
            throw new EndOfStreamException("Can not read from end of Stream.");
        }
        return (Text::Length($Char = $this->Stream->Read(2)) < 2) ? 0x00 : \unpack("s", $Char)[1];
    }

    /**
     * Reads a 2-byte unsigned integer from the current Stream and advances the position of the Stream by two bytes.
     *
     * @throws EndOfStreamException Thrown if the current Stream has reached its end.
     * @return int A 2-byte unsigned integer read from the current Stream.
     */
    public function ReadUint16(): int {
        if($this->Stream->EndOfStream()) {
            throw new EndOfStreamException("Can not read from end of Stream.");
        }
        return (Text::Length($Char = $this->Stream->Read(2)) < 2) ? 0x00 : \unpack("S", $Char)[1];
    }

    /**
     * Reads a 4-byte signed integer from the current Stream and advances the current position of the Stream by four bytes.
     *
     * @throws EndOfStreamException Thrown if the current Stream has reached its end.
     * @return int A 4-byte signed integer read from the current Stream.
     */
    public function ReadInt32(): int {
        if($this->Stream->EndOfStream()) {
            throw new EndOfStreamException("Can not read from end of Stream.");
        }
        return (Text::Length($Char = $this->Stream->Read(4)) < 4) ? 0x00 : \unpack("l", $Char)[1];
    }

    /**
     * Reads a 4-byte unsigned integer from the current Stream and advances the position of the Stream by four bytes.
     *
     * @throws EndOfStreamException Thrown if the current Stream has reached its end.
     * @return int A 4-byte unsigned integer read from the current Stream.
     */
    public function ReadUInt32(): int {
        if($this->Stream->EndOfStream()) {
            throw new EndOfStreamException("Can not read from end of Stream.");
        }
        return (Text::Length($Char = $this->Stream->Read(4)) < 4) ? 0x00 : \unpack("L", $Char)[1];
    }

    /**
     * Reads an 8-byte signed integer from the current Stream and advances the current position of the Stream by eight bytes.
     *
     * @throws EndOfStreamException Thrown if the current Stream has reached its end.
     * @return int An 8-byte signed integer read from the current Stream.
     */
    public function ReadInt64(): int {
        if($this->Stream->EndOfStream()) {
            throw new EndOfStreamException("Can not read from end of Stream.");
        }
        return (Text::Length($Char = $this->Stream->Read(8)) < 8) ? 0x00 : \unpack("q", $Char)[1];
    }

    /**
     * Reads an 8-byte unsigned integer from the current Stream and advances the position of the Stream by eight bytes.
     *
     * @throws EndOfStreamException Thrown if the current Stream has reached its end.
     * @return int An 8-byte unsigned integer read from the current Stream.
     */
    public function ReadUInt64(): int {
        if($this->Stream->EndOfStream()) {
            throw new EndOfStreamException("Can not read from end of Stream.");
        }
        return (Text::Length($Char = $this->Stream->Read(8)) < 8) ? 0x00 : \unpack("Q", $Char)[1];
    }

    /**
     * Reads an 4-byte floating point value from the current Stream and advances the current position of the Stream by four bytes.
     *
     * @throws EndOfStreamException Thrown if the current Stream has reached its end.
     * @return float An 4-byte floating point value read from the current Stream.
     */
    public function ReadFloat(): float {
        if($this->Stream->EndOfStream()) {
            throw new EndOfStreamException("Can not read from end of Stream.");
        }
        return (Text::Length($Char = $this->Stream->Read(4)) < 4) ? 0x00 : \unpack("f", $Char)[1];
    }

    /**
     * Reads an 8-byte floating point value from the current Stream and advances the current position of the Stream by eight bytes.
     *
     * @throws EndOfStreamException Thrown if the current Stream has reached its end.
     * @return float An 8-byte floating point value read from the current Stream.
     */
    public function ReadDouble(): float {
        if($this->Stream->EndOfStream()) {
            throw new EndOfStreamException("Can not read from end of Stream.");
        }
        return (Text::Length($Char = $this->Stream->Read(8)) < 8) ? 0x00 : \unpack("d", $Char)[1];
    }

    /**
     * Reads the next character from the current Stream and advances the current position of the Stream
     * in accordance with the encoding used and the specific character being read from the Stream.
     *
     * @throws EndOfStreamException Thrown if the current Stream has reached its end.
     * @return string A character read from the current Stream.
     */
    public function ReadCharacter(): string {
        if($this->Stream->EndOfStream()) {
            throw new EndOfStreamException("Can not read from end of Stream.");
        }

        switch($this->Encoding) {
            case Encoding::ASCII:
                return \chr($this->ReadByte());
            case Encoding::UTF8:
                $Chars = [
                    $this->ReadByte()
                ];

                // Return if the high-bit is set in the first read character.
                // UTF-8 sequences start with ASCII-values below 128.
                if($Chars[0] & 128) {
                    return Text::Empty;
                }

                // Read a maximum amount of 3 bytes or until the next escape sequence.
                for($i = 0; $i < 3 && !$this->Stream->EndOfStream(); $i++) {

                    $Char = $this->ReadByte();
                    if(!($Char & 128)) {
                        $this->Stream->Seek($this->Stream->Tell() - 1);
                        break;
                    }
                    $Chars[] = $Char;
                }

                // Combine codepoints.
                $Result = "";
                foreach($Chars as $Char) {
                    $Result .= \chr($Char);
                }
                return $Result;
        }
        return "";
    }

    /**
     * Reads a specified amount of characters according the encoding from the current Stream
     * and advances the current position of the Stream by the byte-length of the amount of read characters.
     *
     * @param int $Amount THe amount of characters to read.
     *
     * @throws EndOfStreamException Thrown if the current Stream has reached its end.
     * @return string The characters read from the current Stream.
     */
    public function ReadCharacters(int $Amount = 1): string {
        if($this->Stream->EndOfStream()) {
            throw new EndOfStreamException("Can not read from end of Stream.");
        }
        $Chars = Text::Empty;
        for($i = 0; $i < $Amount && !$this->Stream->EndOfStream(); $i++) {
            $Chars .= $this->ReadCharacter();
        }
        return $Chars;
    }

    /**
     * Reads a Boolean value from the current Stream and advances the current position of the Stream by one byte.
     *
     * @return bool True if the byte is nonzero; otherwise, false.
     */
    public function ReadBoolean(): bool {
        return (bool)$this->ReadByte();
    }

    /**
     * Closes the current BinaryReader and the underlying Stream.
     */
    public function Close(): void {
        $this->Stream->Close();
    }

    /**
     *
     */
    public function __destruct() {
        $this->Close();
    }

}

