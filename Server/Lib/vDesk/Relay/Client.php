<?php
declare(strict_types=1);

namespace vDesk\Relay;

use vDesk\IO\IOException;
use vDesk\IO\Socket;
use vDesk\Security\User;

class Client {
    
    /**
     * The Socket of the Client.
     *
     * @var null|\vDesk\IO\Socket
     */
    private ?Socket $Socket;
    
    /**
     * The User of the Client.
     *
     * @var \vDesk\Security\User
     */
    public User $User;
    
    /**
     * Initializes a new instance of the Client class.
     *
     * @param string $Server   Initializes the Client with the specified Relay server address.
     * @param string $User     Initializes the Client with the specified User name.
     * @param string $Password Initializes the Client with the specified password.
     * @param int    $Timeout  Initializes the Client with the specified timeout.
     *
     * @throws \vDesk\IO\IOException Thrown if the login failed.
     * @throws \vDesk\IO\IOException Thrown if the connection timed out.
     */
    public function __construct(public string $Server, string $User, string $Password, int $Timeout = 3) {
        //Establish connection to Server.
        $this->Socket = new Socket("tcp://{$Server}:3420", Socket::Remote);
        $this->Socket->Write((string)new Event(Event::Login, $User, $Password));
        
        //Wait for response Event.
        foreach(Socket::Select([$this->Socket], [], [], $Timeout)[0] as $Socket) {
            $Event = Event::FromSocket($Socket);
            if($Event->Name === Event::Success) {
                $this->User = new User(Name: $User, Ticket: $Event->Data);
                return;
            }
            throw new IOException($Event->Data);
        }
        throw new IOException("Connection timed out");
    }
    
    /**
     * Dispatches an Event to the Server.
     *
     * @param \vDesk\Relay\Event $Event   The Event to dispatch.
     * @param int                $Timeout The timout in seconds until the Client stops to listen for any response Events.
     *
     * @return null|\vDesk\Relay\Event An instance of any response Event; otherwise, null.
     */
    public function Dispatch(Event $Event, int $Timeout = 3): ?Event {
        $this->Socket->Write((string)$Event);
        foreach(Socket::Select([$this->Socket], [], [], $Timeout)[0] as $Socket) {
            return Event::FromSocket($Socket);
        }
        return null;
    }
    
    /**
     * Listens for an incoming Event from the Server.
     *
     * @param int $Timeout The timout in seconds until the Client stops to listen.
     *
     * @return null|\vDesk\Relay\Event An instance of any dispatched Event; otherwise, null.
     */
    public function Listen(int $Timeout = 3): ?Event {
        foreach(Socket::Select([$this->Socket], [], [], $Timeout)[0] as $Socket) {
            return Event::FromSocket($Socket);
        }
        return null;
    }
    
}