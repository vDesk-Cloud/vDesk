<?php
declare(strict_types=1);

namespace vDesk\IO;

/**
 * Class that represents a bytestream over an udp or tcp/ip socket.
 *
 * @package vDesk\IO
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class Socket extends FileStream {
    
    /**
     * Local (server) Socket type.
     */
    public const Local = 0;
    
    /**
     * Remote (client) Socket type.
     */
    public const Remote = 1;
    
    /**
     * The type of the Socket.
     *
     * @var int
     */
    public int $Type = self::Remote;
    
    /**
     * Initializes a new instance of the Socket class.
     *
     * @param null|string $Address Initializes the Socket with the specified target address.
     * @param null|int    $Type    Initializes the Socket with the specified type.
     */
    public function __construct(?string $Address = null, int $Type = null) {
        parent::__construct();
        if($Address !== null) {
            $this->Open($Address, $Type ?? self::Remote);
        }
    }
    
    /**
     * @inheritDoc
     * @throws \vDesk\IO\SocketException Thrown if a connection cannot be established.
     */
    public function Open(string $Address, int $Type = self::Remote): bool {
        $this->Type = $Type;
        if($Type === self::Local) {
            $this->Pointer = \stream_socket_server($Address, $Error, $Message);
        } else {
            $this->Pointer = \stream_socket_client($Address, $Error, $Message);
        }
        if($this->Pointer === false) {
            throw new SocketException("{$Error} - {$Message}");
        }
        \stream_set_blocking($this->Pointer, true);
        return true;
    }
    
    /**
     * Selects the updated Sockets for non blocking IO operations from a specified set of collections.
     *
     * @param \vDesk\IO\Socket[] $Read         The Sockets to select for non blocking read operations.
     * @param \vDesk\IO\Socket[] $Write        The Sockets to select for non blocking write operations.
     * @param \vDesk\IO\Socket[] $OutOfBand    The Sockets to select for non blocking "out-of-band"-transmission.
     * @param null|int           $Seconds      The amount of seconds to wait for changes on the specified Sockets.
     * @param null|int           $MicroSeconds The amount of microseconds to wait for changes on the specified Sockets.
     *
     * @return array An array containing a subset of the Sockets that wouldn't cause a block on IO operations.
     */
    public static function Select(iterable $Read = [], iterable $Write = [], iterable $OutOfBand = [], ?int $Seconds = null, ?int $MicroSeconds = null): array {
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
            foreach($MayRead as $Pointer) {
                foreach($Read as $Socket) {
                    if($Socket->Pointer === $Pointer) {
                        $CanRead[] = $Socket;
                        break;
                    }
                }
            }
            foreach($MayWrite as $Pointer) {
                foreach($Write as $Socket) {
                    if($Socket->Pointer === $Pointer) {
                        $CanWrite[] = $Socket;
                        break;
                    }
                }
            }
            foreach($MayReceive as $Pointer) {
                foreach($OutOfBand as $Socket) {
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
     * @param null|int $Seconds      The amount of seconds to wait for incoming connections.
     * @param null|int $MicroSeconds The amount of microseconds to wait for incoming connections.
     *
     * @return null|\vDesk\IO\Socket A Socket representing any established connection; otherwise, null.
     */
    public function Accept(?int $Seconds = null, ?int $MicroSeconds = null): ?Socket {
        $Read = [$this->Pointer];
        $_    = [];
        if(\stream_select($Read, $_, $_, $Seconds, $MicroSeconds)) {
            return self::FromPointer(\stream_socket_accept($this->Pointer));
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
     * @param int $Stream
     *
     * @return bool
     */
    public function Shutdown(int $Stream = \STREAM_SHUT_RDWR): bool {
        return \stream_socket_shutdown($this->Pointer, $Stream);
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
}