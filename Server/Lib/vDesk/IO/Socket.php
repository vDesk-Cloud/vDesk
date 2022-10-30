<?php
declare(strict_types=1);

namespace vDesk\IO;

use vDesk\IO\Stream\Readable;
use vDesk\IO\Stream\Writable;
use vDesk\Struct\Properties;

/**
 * Class that represents a bytestream over an udp or tcp/ip socket.
 *
 * @property-read resource $Pointer  Gets the underlying pointer of the Socket.
 * @property bool          $Blocking Gets or sets a flag indicating whether the Socket is blocking.
 *
 * @package vDesk
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class Socket implements IReadableStream, IWritableStream {

    use Properties, Readable, Writable;

    /**
     * Local (server) Socket type.
     */
    public const Local = 0;

    /**
     * Remote (client) Socket type.
     */
    public const Remote = 1;

    /**
     * Initializes a new instance of the Socket class.
     *
     * @param null|string $Address  Initializes the Socket with the specified target address.
     * @param int         $Type     Initializes the Socket with the specified type.
     * @param bool        $Blocking Initializes the Socket with the specified flag indicating whether any operations would block.
     *
     * @throws \vDesk\IO\SocketException Thrown if a connection cannot be established.
     */
    public function __construct(public ?string $Address = null, public int $Type = self::Remote, protected bool $Blocking = true) {
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

        if($Address !== null) {
            if($Type === self::Local) {
                $this->Pointer = \stream_socket_server($Address, $Error, $Message);
            } else {
                $this->Pointer = \stream_socket_client($Address, $Error, $Message);
            }
            if($this->Pointer === false) {
                throw new SocketException($Message, $Error);
            }
            \stream_set_blocking($this->Pointer, $Blocking);
        }

    }

    /** @inheritDoc */
    public function CanRead(): bool {
        return true;
    }

    /** @inheritDoc */
    public function CanWrite(): bool {
        return true;
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
            $MayRead[] = $Socket->Pointer;
        }
        foreach($Write as $Socket) {
            $MayWrite[] = $Socket->Pointer;
        }
        foreach($OutOfBand as $Socket) {
            $MayReceive[] = $Socket->Pointer;
        }
        if((bool)\stream_select($MayRead, $MayWrite, $MayReceive, $Seconds, $MicroSeconds)) {
            foreach($MayRead as $Socket) {
                foreach($Read as $Pointer) {
                    if($Socket->Pointer === $Pointer) {
                        $CanRead[] = $Socket;
                        break;
                    }
                }
            }
            foreach($MayWrite as $Socket) {
                foreach($Write as $Pointer) {
                    if($Socket->Pointer === $Pointer) {
                        $CanWrite[] = $Socket;
                        break;
                    }
                }
            }
            foreach($MayReceive as $Socket) {
                foreach($OutOfBand as $Pointer) {
                    if($Socket->Pointer === $Pointer) {
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
        $Socket = false;
        if($MicroSeconds > 0) {
            $_    = [];
            $Read = [$this->Pointer];
            if(\stream_select($Read, $_, $_, $Seconds, $MicroSeconds)) {
                $Socket = \stream_socket_accept($this->Pointer);
            }
        } else {
            $Socket = \stream_socket_accept($this->Pointer, (float)$Seconds);
        }
        if($Socket === false) {
            return null;
        }
        return self::FromPointer($Socket, static::Local, $this->Blocking);
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
            return \stream_socket_recvfrom($this->Pointer, $Amount, \STREAM_OOB);
        }
        return \stream_socket_recvfrom($this->Pointer, $Amount);
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
            return \stream_socket_recvfrom($this->Pointer, $Amount, \STREAM_PEEK | \STREAM_OOB);
        }
        return \stream_socket_recvfrom($this->Pointer, $Amount, \STREAM_PEEK);
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
            return \stream_socket_sendto($this->Pointer, $Data, \STREAM_OOB);
        }
        return \stream_socket_sendto($this->Pointer, $Data);
    }

    /**
     * Shuts the channels of the Socket down.
     *
     * @param int $Channels The channels to shut down. Must be one of \STREAM_SHUT_*-constants.
     *
     * @return bool True if the specified channels have been shut down; otherwise, false.
     */
    public function Shutdown(int $Channels = \STREAM_SHUT_RDWR): bool {
        return \stream_socket_shutdown($this->Pointer, $Channels);
    }

    /** @inheritDoc */
    public static function FromPointer($Pointer, int $Mode, bool $Blocking = true): static {
        $Socket           = new static(null, $Mode);
        $Socket->Pointer  = $Pointer;
        $Socket->Blocking = $Blocking;
        return $Socket;
    }

}