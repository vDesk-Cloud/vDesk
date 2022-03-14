<?php
declare(strict_types=1);

namespace vDesk\IO;

use vDesk\IO\Stream\Lock;
use vDesk\Struct\Properties;

/**
 * Class that represents a bytestream over an udp or tcp/ip socket.
 *
 * @package vDesk
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class Socket implements IReadableStream, IWritableStream {

    use Properties;

    /**
     * Local (server) Socket type.
     */
    public const Local = 0;

    /**
     * Remote (client) Socket type.
     */
    public const Remote = 1;

    /**
     * The underlying pointer of the Socket.
     *
     * @var resource
     */
    protected $Socket;

    /**
     * Initializes a new instance of the Socket class.
     *
     * @param null|string $Address Initializes the Socket with the specified target address.
     * @param int         $Type    Initializes the Socket with the specified type.
     *
     * @throws \vDesk\IO\SocketException Thrown if a connection cannot be established.
     */
    public function __construct(public ?string $Address = null, public int $Type = self::Remote) {
        $this->AddProperty("Socket", [\Get => fn() => $this->Socket]);
        if($Address !== null) {
            if($Type === self::Local) {
                $this->Socket = \stream_socket_server($Address, $Error, $Message);
            } else {
                $this->Socket = \stream_socket_client($Address, $Error, $Message);
            }
            if($this->Socket === false) {
                throw new SocketException("{$Error} - {$Message}");
            }
            \stream_set_blocking($this->Socket, true);
        }

    }

    /**
     * Selects the updated Sockets for non-blocking IO operations from a specified set of collections.
     *
     * @param \vDesk\IO\Socket[] $Read         The Sockets to select for non-blocking read operations.
     * @param \vDesk\IO\Socket[] $Write        The Sockets to select for non-blocking write operations.
     * @param \vDesk\IO\Socket[] $OutOfBand    The Sockets to select for non-blocking "out-of-band"-transmission.
     * @param int                $Seconds      The amount of seconds to wait for changes on the specified Sockets.
     * @param int                $MicroSeconds The amount of microseconds to wait for changes on the specified Sockets.
     *
     * @return array An array containing a subset of the Sockets that wouldn't cause a block on IO operations.
     */
    public static function Select(iterable $Read = [], iterable $Write = [], iterable $OutOfBand = [], int $Seconds = 0, int $MicroSeconds = 0): array {
        $MayRead = $CanRead = $MayWrite = $CanWrite = $MayReceive = $CanReceive = [];
        foreach($Read as $Socket) {
            $MayRead[] = $Socket->Socket;
        }
        foreach($Write as $Socket) {
            $MayWrite[] = $Socket->Socket;
        }
        foreach($OutOfBand as $Socket) {
            $MayReceive[] = $Socket->Socket;
        }
        if((bool)\stream_select($MayRead, $MayWrite, $MayReceive, $Seconds, $MicroSeconds)) {
            foreach($MayRead as $Socket) {
                foreach($Read as $Socket) {
                    if($Socket->Socket === $Socket) {
                        $CanRead[] = $Socket;
                        break;
                    }
                }
            }
            foreach($MayWrite as $Socket) {
                foreach($Write as $Socket) {
                    if($Socket->Socket === $Socket) {
                        $CanWrite[] = $Socket;
                        break;
                    }
                }
            }
            foreach($MayReceive as $Socket) {
                foreach($OutOfBand as $Socket) {
                    if($Socket->Socket === $Socket) {
                        $CanReceive[] = $Socket;
                        break;
                    }
                }
            }
        }
        return [$CanRead, $CanWrite, $CanReceive];
    }

    /**
     * Waits and accepts any connection on the Socket and returns a new Socket for the established connection.
     *
     * @param int $Seconds      The amount of seconds to wait for incoming connections.
     * @param int $MicroSeconds The amount of microseconds to wait for incoming connections.
     *
     * @return null|\vDesk\IO\Socket A Socket representing any established connection; otherwise, null.
     */
    public function Accept(int $Seconds = 0, int $MicroSeconds = 0): ?Socket {
        $Read = [$this->Socket];
        $_    = [];
        if(\stream_select($Read, $_, $_, $Seconds, $MicroSeconds)) {
            return self::FromSocket(\stream_socket_accept($this->Socket));
        }
        return null;
    }

    /**
     * Receives a specified amount of bytes from the Socket.
     *
     * @param int  $Amount    The amount of bytes to receive.
     * @param bool $OutOfBand Determines whether to receive out of band data.
     *
     * @return string The received data.
     */
    public function Receive(int $Amount = IStream::DefaultChunkSize, bool $OutOfBand = false): string {
        if($OutOfBand) {
            return \stream_socket_recvfrom($this->Socket, $Amount, \STREAM_OOB);
        }
        return \stream_socket_recvfrom($this->Socket, $Amount);
    }

    /**
     * Reads a specified amount of bytes from the Socket without consuming the data.
     *
     * @param int  $Amount    The amount of bytes to read.
     * @param bool $OutOfBand Determines whether to receive out of band data.
     *
     * @return string The read data.
     */
    public function Peek(int $Amount, bool $OutOfBand = false): string {
        if($OutOfBand) {
            return \stream_socket_recvfrom($this->Socket, $Amount, \STREAM_PEEK | \STREAM_OOB);
        }
        return \stream_socket_recvfrom($this->Socket, $Amount, \STREAM_PEEK);
    }

    /**
     * Sends data over the Socket.
     *
     * @param string $Data      The data to send.
     * @param bool   $OutOfBand Determines whether to send the data out of band.
     *
     * @return int The amount of bytes sent.
     */
    public function Send(string $Data, bool $OutOfBand = false): int {
        if($OutOfBand) {
            return \stream_socket_sendto($this->Socket, $Data, \STREAM_OOB);
        }
        return \stream_socket_sendto($this->Socket, $Data);
    }

    /**
     * Shuts the channels of the Socket down.
     *
     * @param int $Stream
     *
     * @return bool
     */
    public function Shutdown(int $Stream = \STREAM_SHUT_RDWR): bool {
        return \stream_socket_shutdown($this->Socket, $Stream);
    }

    /**
     * @inheritDoc
     */
    public function CanSeek(): bool {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function CanRead(): bool {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function CanWrite(): bool {
        return true;
    }

    /**
     * Reads a given amount of bytes from the Socket.
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
        return \fread($this->Socket, $Amount);
    }

    /**
     * Reads a line from the Socket.
     *
     * @return string The read line.
     * @throws EndOfStreamException Thrown if the Stream has reached its end.
     */
    public function ReadLine(): string {
        if($this->EndOfStream()) {
            throw new EndOfStreamException("Can not read from end of Stream.");
        }
        return \fgets($this->Socket);
    }

    /**
     * Reads the entire content of the Socket from the current position until the end of the Socket.
     *
     * @param int $Amount The amount of bytes to read. If $Amount is set to -1, the entire content is read until the end of the Socket.
     * @param int $Offset The offset to start reading from. If $Offset is set to -1, reading starts from the current position of the
     *                    Socket.
     *
     * @return string The read content.
     * @throws EndOfStreamException Thrown if the Stream has reached its end.
     */
    public function ReadAll(int $Amount = -1, int $Offset = -1): string {
        if($this->EndOfStream()) {
            throw new EndOfStreamException("Can not read from end of Stream.");
        }
        return \stream_get_contents($this->Socket, $Amount, $Offset);
    }

    /**
     * Reads a single character from the Socket.
     *
     * @return string The read character.
     * @throws EndOfStreamException Thrown if the Stream has reached its end.
     */
    public function ReadCharacter(): string {
        if($this->EndOfStream()) {
            throw new EndOfStreamException("Can not read from end of Stream.");
        }
        return \fgetc($this->Socket);
    }

    /**
     * Creates a new Socket as wrapper of a specified pointer resource.
     *
     * @param resource $Socket The pointer to wrap.
     *
     * @return \vDesk\IO\Socket A new instance of the Socket yielding the specified pointer.
     */
    public static function FromSocket($Socket): static {
        $Stream         = new static();
        $Stream->Socket = $Socket;
        return $Stream;
    }

    /**
     * Tells whether the current Socket has reached its end.
     * EndOfStream is a convenience method that is equivalent to the value of the EndOfStream property of the current instance.
     *
     * @return bool True if the Socket has reached its end; otherwise, false.
     */
    public function EndOfStream(): bool {
        return \feof($this->Socket);
    }

    /**
     * Sets a lock on the Socket, limiting or prohibiting access for other processes.
     *
     * @param int $Type The type of the lock. Either one value of {@link \vDesk\IO\Stream\Lock}.
     *
     * @return bool True on success, false on failure.
     */
    public function Lock(int $Type = Lock::Shared): bool {
        return \flock($this->Socket, $Type);
    }

    /**
     * Unlocks the FileStream, granting access for other processes.
     *
     * @return bool True on success, false on failure.
     */
    public function Unlock(): bool {
        return \flock($this->Socket, Lock::Free);
    }

    /**
     * Closes the Socket.
     *
     * @return bool True on success, false on failure.
     */
    public function Close(): bool {
        return \is_resource($this->Socket) && \fclose($this->Socket);
    }

    /**
     * Writes data to the Socket.
     *
     * @param string   $Data   The data to write.
     * @param null|int $Amount The amount of bytes to write.
     *
     * @return int The amount of bytes written.
     */
    public function Write(string $Data, ?int $Amount = null): int {
        if($Amount === null) {
            return \fwrite($this->Socket, $Data);
        }
        return \fwrite($this->Socket, $Data, $Amount);
    }

    /**
     * Truncates the sequence of bytes to the specified size.
     *
     * @param int $Size The size to truncate to.
     *
     * @return bool True on success; otherwise, false.
     */
    public function Truncate(int $Size): bool {
        return \ftruncate($this->Socket, $Size);
    }

}