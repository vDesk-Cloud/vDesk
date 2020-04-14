<?php
declare(strict_types=1);

namespace vDesk\IO;

use vDesk\Struct\Properties;
use vDesk\Struct\Text\Encoding;

/**
 * @author Mephisto
 *
 */
class BinaryWriter {

    use Properties;

    /**
     * The supported encodings of the BinaryWriteer.
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
     * The current Stream of the BinaryWriter.
     *
     * @var \vDesk\IO\IStream|null
     */
    protected ?IStream $Stream;

    /**
     * The current encoding of the BinaryWriter.
     *
     * @var string
     */
    protected string $Encoding;

    /**
     * Initializes a new instance of the BinaryWriter class based on the specified stream and character encoding.
     *
     * @param \vDesk\IO\IStream $Stream   $Stream   The input stream.
     * @param string            $Encoding The character encoding to use.
     */
    public function __construct(IStream $Stream, $Encoding = Encoding::UTF8) {
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
        if(!$Stream->CanWrite()) {
            throw new \InvalidArgumentException("The provided Stream doesn't support 'write'-operations.");
        }
        if(!\in_array($Encoding, static::Encodings)) {
            throw new \InvalidArgumentException("'$Encoding' is not a supported encoding.");
        }

        $this->Stream = $Stream;
        $this->Encoding = $Encoding;
    }

    /**
     * Writes an array of values to the current Stream beginning at a specified offset
     * and advances the current position of the Stream by the byte-length of the amount of written values.
     *
     * @param int   $Type   The type of the value to write.
     * @param array $Values The values to write to the current Stream.
     * @param int   $Offset The offset where writing begins.
     *
     * @throws \InvalidArgumentException Thrown if the specified type is invalid.
     */
    public function Write(int $Type, array $Values, int $Offset = null): void {
        if($Offset !== null && $this->Stream->CanSeek()) {
            $this->Stream->Seek($Offset);
        }

        switch($Type) {
            case self::Int8:
                $Method = "WriteByte";
                break;
            case self::UInt8:
                $Method = "WriteSByte";
                break;
            case self::Int16:
                $Method = "WriteInt16";
                break;
            case self::UInt16:
                $Method = "WriteUInt16";
                break;
            case self::Int32:
                $Method = "WriteInt32";
                break;
            case self::UInt32:
                $Method = "WriteUInt32";
                break;
            case self::Int64:
                $Method = "WriteInt64";
                break;
            case self::UInt64:
                $Method = "WriteUInt64";
                break;
            case self::Float:
                $Method = "WriteFloat";
                break;
            case self::Double:
                $Method = "WriteDouble";
                break;
            case self::Boolean:
                $Method = "WriteBoolean";
                break;
            default:
                throw new \InvalidArgumentException("The specified type is invalid.");
                break;
        }

        foreach($Values as $Value) {
            $this->{$Method}($Value);
        }
    }

    /**
     * Writes a 1-byte signed integer from the current Stream and advances the current position of the Stream by one byte.
     * Alias of {@see BinaryWriteer::WriteSByte()}
     *
     * @param int $Value The value to write.
     */
    public function WriteInt8(int $Value): void {
        $this->WriteSByte($Value);
    }

    /**
     * Writes a signed byte from this Stream and advances the current position of the Stream by one byte.
     *
     * @param int $Value The value to write.
     */
    public function WriteSByte(int $Value): void {
        $this->Stream->Write(pack("c", $Value));
    }

    /**
     * Writes a 1-byte unsigned integer from the current Stream and advances the position of the Stream by one byte.
     * Alias of {@see BinaryWriteer::WriteByte()}
     *
     * @param int $Value The value to write.
     */
    public function WriteUInt8(int $Value): void {
        $this->WriteByte($Value);
    }

    /**
     * Writes the next byte from the current Stream and advances the current position of the Stream by one byte.
     *
     * @param int $Value The value to write.
     */
    public function WriteByte(int $Value): void {
        $this->Stream->Write(pack("C", $Value));
    }

    /**
     * Writes a 2-byte signed integer from the current Stream and advances the current position of the Stream by two bytes.
     *
     * @param int $Value The value to write.
     */
    public function WriteInt16(int $Value): void {
        $this->Stream->Write(pack("s", $Value));
    }

    /**
     * Writes a 2-byte unsigned integer from the current Stream and advances the position of the Stream by two bytes.
     *
     * @param int $Value The value to write.
     */
    public function WriteUint16(int $Value): void {
        $this->Stream->Write(pack("S", $Value));
    }

    /**
     * Writes a 4-byte signed integer from the current Stream and advances the current position of the Stream by four bytes.
     *
     * @param int $Value The value to write.
     */
    public function WriteInt32(int $Value): void {
        $this->Stream->Write(pack("l", $Value));
    }

    /**
     * Writes a 4-byte unsigned integer from the current Stream and advances the position of the Stream by four bytes.
     *
     * @param int $Value The value to write.
     */
    public function WriteUInt32(int $Value): void {
        $this->Stream->Write(pack("L", $Value));
    }

    /**
     * Writes an 8-byte signed integer from the current Stream and advances the current position of the Stream by eight bytes.
     *
     * @param int $Value The value to write.
     */
    public function WriteInt64(int $Value): void {
        $this->Stream->Write(pack("q", $Value));
    }

    /**
     * Writes an 8-byte unsigned integer from the current Stream and advances the position of the Stream by eight bytes.
     *
     * @param int $Value The value to write.
     */
    public function WriteUInt64(int $Value): void {
        $this->Stream->Write(pack("Q", $Value));
    }

    /**
     * Writes an 4-byte floating point value from the current Stream and advances the current position of the Stream by four bytes.
     *
     * @param float $Value The value to write.
     */
    public function WriteFloat(float $Value): void {
        $this->Stream->Write(pack("f", $Value));
    }

    /**
     * Writes an 8-byte floating point value from the current Stream and advances the current position of the Stream by eight bytes.
     *
     * @param float $Value The value to write.
     */
    public function WriteDouble(float $Value): void {
        $this->Stream->Write(pack("d", $Value));
    }

    /**
     *
     */
    public function __destruct() {
        $this->Close();
    }

    /**
     * Closes the current BinaryWriter and the underlying Stream.
     */
    public function Close(): void {
        $this->Stream->Close();
    }

}

