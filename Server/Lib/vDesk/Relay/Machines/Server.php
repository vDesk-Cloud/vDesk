<?php
declare(strict_types=1);

namespace vDesk\Machines;

use vDesk\IO\IOException;
use vDesk\IO\Socket;
use vDesk\Modules;
use vDesk\Relay\Event;
use vDesk\Relay\Server\Client;
use vDesk\Relay\Server\EventListener;
use vDesk\Security\UnauthorizedAccessException;
use vDesk\Security\User;
use vDesk\Struct\Collections\Collection;
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
        $this->Socket         = new Socket("tcp://0.0.0.0:3420", Socket::Local);
        $this->User           = \vDesk::$User;
        Log::Info("Relay Server", "Started.");
    }
    
    /**
     * @inheritDoc
     */
    public function Run(): void {
        
        //Check if a new connection has been established.
        $Socket = $this->Socket->Accept(0, 50000);
        if($Socket !== null) {
            //Perform login.
            try {
                $Event = Event::FromSocket($Socket);
                if($Event->Name !== Event::Login) {
                    $Socket->Write((string)new Event(Event::Error, "Server", "Authentication required!"));
                    $Socket->Close();
                }
                $User = Modules::Security()::Login($Event->Sender, $Event->Data);
                $this->Clients->Add(new Client($User, $Socket));
                $Socket->Write((string)new Event(Event::Success, "Server", $User->Ticket));
                Log::Info("Relay Server", "Client: '{$User->Name}' connected.");
                
                //@todo Create Security-Update for this dirty hack...
                \vDesk::$User = $this->User;
            } catch(UnauthorizedAccessException $Exception) {
                $Socket->Write((string)new Event(Event::Error, "Server", $Exception->getMessage()));
            } catch(IOException $Exception) {
                $Socket->Write((string)new Event(Event::Error, "Server", "Malformed Event received!"));
                Log::Error(__METHOD__, $Exception->getMessage());
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
        foreach(Socket::Select($Sockets)[0] as $Socket) {
            //Check if the connection has been closed.
            if($Socket->EndOfStream()) {
                $Client = $this->Clients->Find(static fn(Client $Client): bool => $Client->Socket === $Socket);
                $this->Clients->Remove($Client);
                
                //Remove EventListeners of disconnected Client.
                foreach($this->EventListeners->Filter(static fn(EventListener $Listener): bool => $Listener->Client === $Client) as $Listener) {
                    $this->EventListeners->Remove($Listener);
                }
                
                $Socket->Close();
                Log::Warn("Relay Server", "{$Client?->User?->Name} timed out.");
                continue;
            }
            try {
                $Events[] = Event::FromSocket($Socket);
            } catch(IOException $Exception) {
                $Socket->Write((string)new Event(Event::Error, "Server", "Malformed Event received!"));
                Log::Error(__METHOD__, $Exception->getMessage());
            }
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
                    //@todo Shame on you Kerry, just shame on you...
                    \vDesk::$User = $Client->User;
                    
                    Modules::Security()::Logout();
                    $Client->Socket->Close();
                    $this->Clients->Remove($Client);
                    
                    //Remove EventListeners of disconnected Client.
                    foreach($this->EventListeners->Filter(static fn(EventListener $Listener): bool => $Listener->Client === $Client) as $Listener) {
                        $this->EventListeners->Remove($Listener);
                    }
                    
                    \vDesk::$User = $this->User;
                    Log::Info("Relay Server", "Client: '{$Client->User->Name}' disconnected.");
                    continue 2;
                case Event::AddEventListener:
                    $this->EventListeners->Add(new EventListener($Event->Data, $Client));
                    $Socket->Write((string)new Event(Event::Success, "Server"));
                    Log::Info("Relay Server", "{$Client->User->Name} subscribed to '{$Event->Data}'.");
                    continue 2;
                case Event::RemoveEventListener:
                    $this->EventListeners->Remove(
                        $this->EventListeners->Find(
                            static fn(EventListener $Listener): bool => $Listener->Event === $Event->Data
                                                                        && $Listener->Client->User->Ticket === $Event->Sender
                        )
                    );
                    $Socket->Write((string)new Event(Event::Success, "Server"));
                    Log::Info("Relay Server", "{$Client->User->Name} unsubscribed from '{$Event->Data}'.");
                    continue 2;
                default:
                    Log::Info("Relay Server", "Dispatched Event: {$Event->Name}.");
                    //Replace ticket with name and dispatch to subscribers.
                    $Event->Sender = $Client->User->Name;
                    foreach(
                        $this->EventListeners->Filter(
                            static fn(EventListener $Listener): bool => $Listener->Event === $Event->Name
                        )
                        as $Listener
                    ) {
                        Log::Info("Relay Server", "Dispatched Event to Client: {$Listener->Client->User->Name}.");
                        $Listener->Client->Socket->Write((string)$Event);
                    }
            }
        }
        \usleep(5000);
    }
    
    /**
     * @inheritDoc
     */
    public function Stop(int $Code = 0): void {
        $Event = (string)new Event(Event::Shutdown, "Server", "Shutting down.");
        foreach($this->Clients as $Client) {
            if(!$Client->Socket->EndOfStream()) {
                $Client->Socket->Write($Event);
                
            }
            $Client->Socket->Close();
        }
        $this->Socket->Close();
        Log::Info("Relay Server", "Shutting down.");
        parent::Stop($Code);
    }
}