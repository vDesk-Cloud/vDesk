<?php
declare(strict_types=1);

namespace vDesk\Machines;

use vDesk\IO\Socket;
use vDesk\Modules;
use vDesk\Relay\Event;
use vDesk\Relay\Server\Client;
use vDesk\Security\UnauthorizedAccessException;
use vDesk\Security\User;
use vDesk\Struct\Collections\Collection;
use vDesk\Struct\Collections\Dictionary;
use vDesk\Utils\Log;

/**
 * Relay Events Server.
 *
 * @package vDesk\Relay
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class Server extends Machine {
    
    /**
     * The Listeners of the Server.
     *
     * @var \vDesk\Struct\Collections\Collection
     */
    private Collection $EventListeners;
    
    /**
     * The Clients of the Server.
     *
     * @var \vDesk\Struct\Collections\Collection
     */
    private Collection $Clients;
    
    /**
     * The Socket of the Server.
     *
     * @var \vDesk\IO\Socket
     */
    private Socket $Socket;
    
    /**
     * The Socket of the Server.
     *
     * @var \vDesk\Security\User
     */
    private User $User;
    
    /**
     * @inheritDoc
     */
    public function Start(): void {
        $this->EventListeners = new Collection();
        $this->Clients        = new Collection();
        $this->Socket         = new Socket("tcp://0.0.0.0:420", Socket::Local);
        $this->User           = \vDesk::$User;
        Log::Info(__METHOD__, "Relay server started.");
    }
    
    /**
     * @inheritDoc
     */
    public function Run(): void {
        
        //Check if a new connection has been established.
        $Socket = $this->Socket->Accept(1);
        if($Socket !== null) {
            $Event = Event::FromSocket($Socket);
            if($Event->Name !== Event::Login) {
                $Socket->Write((string)new Event(Event::Error, "Server", "Logg dich erst ein Du Kaschber!"));
                $Socket->Close();
            }
            //Perform login.
            try {
                $User = Modules::Security()::Login($Event->Sender, $Event->Data);
                $this->Clients->Add(new Client($User, $Socket));
    
                Log::Debug(__METHOD__, "Client: '{$User->Name}' connected.");
                
                //@todo Create Security-Update for this dirty hack...
                \vDesk::$User = $this->User;
            } catch(UnauthorizedAccessException $Exception) {
                $Socket->Write((string)new Event(Event::Error, "Server", $Exception->getMessage()));
            }
        }
        
        //Idle if no clients are connected.
        if($this->Clients->Count === 0) {
            \usleep(50000);
            return;
        }
        
        //Parse new Events.
        $Sockets = [];
        foreach($this->Clients as $Client) {
            $Sockets[] = $Client->Socket;
        }
        $Events = [];
        foreach(Socket::Select($Sockets)["Read"] as $Socket) {
            $Events[] = Event::FromSocket($Socket);
        }
        
        //Process Events.
        foreach($Events as $Event) {
            
            //Get sender.
            /** @var \vDesk\Relay\Server\Client $Client */
            $Client = $this->Clients->Find(static fn(Client $Client): bool => $Client->User->Ticket === $Event->Sender);
            if($Client === null) {
                continue;
            }
            
            switch($Event->Name) {
                case Event::Logout:
                    Log::Info(__METHOD__, "Client: '{$Client->User->Name}' disconnected.");
                    //@todo Shame on you Kerry, just shame on you...
                    \vDesk::$User = $Client->User;
                    
                    Modules::Security()::Logout();
                    $Client->Socket->Close();
                    $this->Clients->Remove($Client);
                    
                    \vDesk::$User = $this->User;
                    
                    break;
                case Event::AddEventListener:
                    Log::Info(__METHOD__, "Adding: {$Event->Data}.");
                    $this->EventListeners->Add(new Event\Listener($Event->Name, $Client));
                    break;
                case Event::RemoveEventListener:
                    Log::Info(__METHOD__, "Removing: {$Event->Data}.");
                    $this->EventListeners->Remove(
                        $this->EventListeners->Find(
                            static fn(Event\Listener $Listener): bool => $Listener->Event === $Event->Data
                                                                         && $Listener->Client->User->Ticket === $Event->Sender
                        )
                    );
                    break;
                default:
                    Log::Info(__METHOD__, "Dispatched: {$Event->Name}.");
                    //Replace ticket with name and dispatch to subscribers.
                    $Event->Sender = $Client->User->Name;
                    foreach(
                        $this->EventListeners->Filter(
                            static fn(Event\Listener $Listener): bool => $Listener->Event === $Event->Name
                        )
                        as $Listener
                    ) {
                        $Listener->Client->Socket->Write((string)$Event);
                    }
            }
        }
        \usleep(50000);
    }
    
    /**
     * @inheritDoc
     */
    public function Stop(int $Code = 0): void {
        $Event = new Event(Event::Shutdown, "Server", "Meddl off!");
        foreach($this->Clients as $Socket) {
            $Socket->Write((string)$Event);
        }
        $this->Socket->Close();
        Log::Info(__METHOD__, "Relay server shutting down.");
        parent::Stop($Code);
    }
}