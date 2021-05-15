<?php
declare(strict_types=1);

namespace vDesk\Relay;

use vDesk\IO\IOException;
use vDesk\IO\Socket;
use vDesk\Struct\Text;

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
    public const Namespace = "vDesk.Relay.";
    
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
     * @param \vDesk\IO\Socket $Socket The Socket to parse the Event from.
     *
     * @return \vDesk\Relay\Event An Event parsed from the specified Socket.
     *
     * @throws \vDesk\IO\IOException Thrown if the sender of the Event is missing.
     * @throws \vDesk\IO\IOException Thrown if the data of the Event is missing.
     * @throws \JsonException Thrown if the data of the Event is malformed.
     */
    public static function FromSocket(Socket $Socket): self {
        
        //Parse name.
        $Name = $Socket->ReadLine();
        if(Text::IsNullOrWhitespace($Name)) {
            throw new IOException("Missing name for Event!");
        }
        
        //Parse sender.
        if($Socket->EndOfStream()) {
            throw new IOException("Missing sender for Event: \"{$Name}\"!");
        }
        $Sender = $Socket->ReadLine();
        if(Text::IsNullOrWhitespace($Sender)) {
            throw new IOException("Missing sender for Event: \"{$Name}\"!");
        }
        
        //Parse data.
        if($Socket->EndOfStream()) {
            throw new IOException("Missing data for Event: \"{$Name}\" from Client: \"{$Name}\"!");
        }
        $Data = $Socket->ReadLine();
        if(Text::IsNullOrWhitespace($Data)) {
            throw new IOException("Missing data for Event: \"{$Name}\" from Client: \"{$Name}\"!");
        }
        
        //Discard last delimiter.
        if(!$Socket->EndOfStream()) {
            $Socket->ReadLine();
        }
        return new static(
            \rtrim($Name, self::Delimiter),
            \rtrim($Sender, self::Delimiter),
            \json_decode(\rtrim($Data, self::Delimiter), true, 512, \JSON_THROW_ON_ERROR)
        );
    }
    
    /**
     * Creates a transmittable string representation of the Event.
     *
     * @return string A transmittable string representation of the Event.
     * @throws \JsonException Thrown if the data of the Event is malformed.
     */
    public function __toString() {
        return $this->Name . self::Delimiter
               . $this->Sender . self::Delimiter
               . \json_encode($this->Data, \JSON_THROW_ON_ERROR) . self::Delimiter . self::Delimiter;
    }
    
}