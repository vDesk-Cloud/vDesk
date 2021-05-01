<?php
declare(strict_types=1);

namespace vDesk\Machines;

use vDesk\IO\Socket;
use vDesk\Modules;
use vDesk\Relay\Event;
use vDesk\Struct\Collections\Collection;
use vDesk\Struct\Collections\Dictionary;
use vDesk\Utils\Log;

/**
 * Machine that schedules Tasks.
 *
 * @package vDesk\Machines
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class Server extends Machine {
    
    /**
     * The Tasks of the Machine.
     *
     * @var \vDesk\Struct\Collections\Collection
     */
    private Collection $EventListeners;
    
    /**
     * The clients of the Server.
     *
     * @var \vDesk\Struct\Collections\Dictionary
     */
    private Dictionary $Clients;
    
    /**
     * The Socket of the Server.
     *
     * @var \vDesk\IO\Socket
     */
    private Socket $Socket;
    
    /**
     * @inheritDoc
     */
    public function Start(): void {
        $this->EventListeners = new Collection();
        $this->Clients        = new Dictionary();
        Log::Debug(__METHOD__, "0");
        $this->Socket = new Socket("tcp://0.0.0.0:420", Socket::Local);
        Log::Debug(__METHOD__, "1");
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
                $Socket->Write("Logg dich erst ein Du Kaschber!\n");
                $Socket->Close();
            }
            //Perform login.
            $User = Modules::Security()::Login($Event->Sender, $Event->Data);
            $this->Clients->Add($User->Ticket, $Socket);
        }
        Log::Debug(__METHOD__, "2");
        //Parse new Events.
        $Events = [];
        foreach(Socket::Select($this->Clients)["Read"] as $Socket) {
            $Events[] = Event::FromSocket($Socket);
        }
        Log::Debug(__METHOD__, "3");
        //Process Events.
        foreach($Events as $Event) {
            switch($Event->Name) {
                case Event::Logout:
                    Modules::Security()::Logout($Event->Sender);
                    $this->Clients[$Event->Sender]->Close();
                    $this->Clients->Remove($Event->Sender);
                    break;
                case Event::AddEventListener:
                    $this->EventListeners->Add(new Event\Listener($Event->Name, $Event->Sender, $this->Clients[$Event->Sender]));
                    break;
                case Event::RemoveEventListener:
                    $this->EventListeners->Remove(
                        $this->EventListeners->Find(
                            static fn(Event\Listener $Listener): bool => $Listener->Event === $Event->Data && $Listener->User === $Event->Sender
                        )
                    );
                    break;
                default:
                    foreach($this->EventListeners->Filter(static fn(Event\Listener $Listener): bool => $Listener->Event === $Event->Name) as $Listener) {
                        $Listener->Socket->Write((string)$Event);
                    }
            }
        }
        Log::Debug(__METHOD__, "4");
        \usleep(100000);
    }
    
    /**
     * @inheritDoc
     */
    public function Stop(int $Code = 0): void {
        $Event = new Event(Event::Shutdown, "Meddl");
        foreach($this->Clients as $Socket) {
            $Socket->Write((string)$Event);
        }
        parent::Stop($Code);
    }
}