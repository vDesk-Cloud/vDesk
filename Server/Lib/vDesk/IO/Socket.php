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
     * Blah
     *
     * @param $Read
     * @param $Write
     * @param $Error
     */
    public static function Select(&$Read, &$Write, &$Error): void {

    }
    
    public function Accept(): ?IStream {
        
        $Read   = [$this->Pointer];
        $Write  = [];
        $Delete = [];
        if(\stream_select($Read, $Write, $Delete, 0)) {
            $Pointer = \stream_socket_accept($this->Pointer);
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