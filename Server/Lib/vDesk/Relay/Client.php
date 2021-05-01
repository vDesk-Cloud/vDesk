<?php
declare(strict_types=1);

namespace vDesk\Relay;

use vDesk\IO\Socket;

class Client {
    
    /**
     * Client constructor.
     *
     * @param string           $Server
     * @param string           $User
     * @param string           $Password
     * @param \vDesk\IO\Socket $Socket
     */
    public function __construct(public string $Server, public string $User, string $Password, private Socket $Socket) {
        $this->Socket = new Socket("tcp://{$Server}:420", Socket::Remote);
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
        foreach(Socket::Select([$this->Socket], [], [], $Timeout)["Read"] as $Socket) {
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
        foreach(Socket::Select([$this->Socket], [], [], $Timeout)["Read"] as $Socket) {
            return Event::FromSocket($Socket);
        }
        return null;
    }
    
}