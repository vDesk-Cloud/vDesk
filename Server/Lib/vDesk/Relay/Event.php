<?php
declare(strict_types=1);

namespace vDesk\Relay;

use vDesk\IO\Socket;

/**
 * Class that represents a distributed Event.
 *
 * @package vDesk\Relay
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
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
     * Predefined Event to indicate successful operations.
     */
    public const Success = self::Namespace . "Success";
    
    /**
     * Predefined Event to indicate occurred errors.
     */
    public const Error = self::Namespace . "Error";
    
    /**
     * Predefined Event to inform the clients the Relay server is shutting down.
     */
    public const Shutdown = self::Namespace . "Shutdown";
    
    /**
     * The delimiter of Events.
     */
    public const Delimiter = "\n";
    
    /**
     * Initializes a new instance of the Event class.
     *
     * @param null|string $Name   Initializes the Event with the specified name.
     * @param null|string $Sender Initializes the Event with the specified sender.
     * @param mixed       $Data   Initializes the Event with the specified data.
     */
    public function __construct(public ?string $Name = null, public ?string $Sender = null, public $Data = null) {
    }
    
    /**
     * Parses an Event from a specified Socket.
     *
     * @param \vDesk\IO\Socket $Socket
     *
     * @return \vDesk\Relay\Event
     * @throws \JsonException
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
    
    /**
     * Creates a transmittable string representation of the Event.
     *
     * @return string
     * @throws \JsonException
     */
    public function __toString() {
        return $this->Name . self::Delimiter
               . $this->Sender . self::Delimiter
               . \json_encode($this->Data, \JSON_THROW_ON_ERROR) . self::Delimiter . self::Delimiter;
    }
    
}