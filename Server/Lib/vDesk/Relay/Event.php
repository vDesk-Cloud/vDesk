<?php
declare(strict_types=1);

namespace vDesk\Relay;

use vDesk\IO\Socket;

class Event {
    
    /**
     * The namespace of predefined Events.
     */
    private const Namespace = "vDesk.Relay.";
    
    /**
     * Predefined Event to login on a Relay server.
     */
    public const Login = self::Namespace . "Login";
    
    /**
     * Predefined Event to logout from a Relay server.
     */
    public const Logout = self::Namespace . "Logout";
    
    /**
     * Predefined Event for adding event listeners.
     */
    public const AddEventListener = self::Namespace . "AddEventListener";
    
    /**
     * Predefined Event for removing event listeners.
     */
    public const RemoveEventListener = self::Namespace . "RemoveEventListener";
    
    /**
     * Predefined Event to inform the clients the Relay server is shutting down.
     */
    public const Shutdown = self::Namespace . "Shutdown";
    
    public const Delimiter = "\n";
    
    /**
     * Message constructor.
     *
     * @param null|string $Name
     * @param null|string $Sender
     * @param null|string $Data
     */
    public function __construct(
        public ?string $Name = null,
        public ?string $Sender = null,
        public ?string $Data = null
    ) {
    
    }
    
    /**
     * Parses an Event from a specified Socket.
     *
     * @param \vDesk\IO\Socket $Socket
     *
     * @return \vDesk\Relay\Event
     */
    public static function FromSocket(Socket $Socket): self {
        $Name   = $Socket->ReadLine();
        $Sender = $Socket->ReadLine();
        $Data   = $Socket->ReadLine();
        //Discard last delimiter.
        $Socket->ReadLine();
        return new static(
            \rtrim($Name, self::Delimiter),
            \rtrim($Sender, self::Delimiter),
            \json_decode(\rtrim($Data, self::Delimiter), true, 512, \JSON_THROW_ON_ERROR)
        );
    }
    
    public function __toString() {
        return $this->Name . self::Delimiter
               . $this->Sender . self::Delimiter
               . \json_encode($this->Data, \JSON_THROW_ON_ERROR) . self::Delimiter . self::Delimiter;
        
    }
    
}