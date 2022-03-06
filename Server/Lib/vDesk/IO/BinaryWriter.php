<?php
declare(strict_types=1);

namespace vDesk\IO;

use vDesk\Struct\Properties;
use vDesk\Struct\Text\Encoding;

/**
 * Writes primitive data types as binary values in a specific encoding.
 *
 * @package vDesk
 * @author  Kerry <DevelopmentHero@gmail.com>
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
     * @param \vDesk\IO\IWritableStream $Stream   $Stream   The input stream.
     * @param string                    $Encoding The character encoding to use.
     */
    public function __construct(IWritableStream $Stream, string $Encoding = Encoding::UTF8) {
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
        if(!\in_array($Encoding, static::Encodings)) {
            throw new \InvalidArgumentException("'$Encoding' is not a supported encoding.");
        }

        $this->Stream   = $Stream;
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
        if($Offset !== null && $this->Stream instanceof ISeekableStream) {
            $this->Stream->Seek($Offset);
        }

        $Method = match ($Type) {
            self::Int8 => "WriteByte",
            self::UInt8 => "WriteSByte",
            self::Int16 => "WriteInt16",
            self::UInt16 => "WriteUInt16",
            self::Int32 => "WriteInt32",
            self::UInt32 => "WriteUInt32",
            self::Int64 => "WriteInt64",
            self::UInt64 => "WriteUInt64",
            self::Float => "WriteFloat",
            self::Double => "WriteDouble",
            self::Boolean => "WriteBoolean",
            default => throw new \InvalidArgumentException("The specified type is invalid."),
        };

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
    }

    /**
     * Closes the current BinaryWriter and the underlying Stream.
     */
    public function Close(): void {
        $this->Stream->Close();
    }

}

