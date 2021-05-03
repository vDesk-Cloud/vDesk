<?php
declare(strict_types=1);

namespace vDesk\Machines;

use vDesk\Configuration\Settings;
use vDesk\DataProvider\Expression;
use vDesk\Relay\Client\EventListener;
use vDesk\IO\IOException;
use vDesk\IO\Path;
use vDesk\Relay\Event;
use vDesk\Struct\Collections\Collection;
use vDesk\Utils\Log;

/**
 * Process based Relay client.
 *
 * @package vDesk\Relay
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class Client extends Machine {
    
    /**
     * The Relay Client of the Machine.
     *
     * @var \vDesk\Relay\API\Client
     */
    private \vDesk\Relay\API\Client $Client;
    
    /**
     * @var \vDesk\Struct\Collections\Collection
     */
    private Collection $EventListeners;
    
    /**
     * @inheritDoc
     */
    public function Start(): void {
        //Connect to server.
        try {
            $this->Client = new \vDesk\Relay\API\Client(
                Settings::$Local["Relay"]["Server"],
                Settings::$Local["Relay"]["Port"],
                Settings::$Local["Relay"]["Client"],
                Settings::$Local["Relay"]["Password"]
            );
        } catch(IOException $Exception) {
            Log::Error(__METHOD__, $Exception->getMessage());
            $this->Stop($Exception->getCode());
        }
        Log::Info("Relay Client", "Started.");
        
        //Load EventListeners.
        $this->EventListeners = new Collection();
        foreach(
            Expression::Select("File")
                      ->From("Archive.Elements")
                      ->Where(
                          [
                              "Parent"    => Settings::$Local["Events"]["Directory"],
                              "Extension" => "php",
                              "Name"      => ["LIKE" => Event::Namespace . "%"]
                          ]
                      )
            as $Listener
        ) {
            $this->EventListeners->Add(include Settings::$Local["Archive"]["Directory"] . Path::Separator . $Listener["File"]);
        }
        
        //Subscribe EventListeners on the server.
        foreach($this->EventListeners as $EventListener) {
            $Response = $this->Client->Dispatch(new Event(Event::AddEventListener, $this->Client->User->Ticket, $EventListener->Event));
            if($Response !== null && $Response->Name === Event::Success) {
                Log::Info("Relay Client", "Client \"{$this->Client->User->Name}\" subscribed to event \"{$EventListener->Event}\".");
            }
        }
    }
    
    /**
     * @inheritDoc
     */
    public function Run(): void {
        //Check if an Event has been dispatched.
        $Event = $this->Client->Listen(1);
        if($Event === null) {
            \usleep(50000);
            return;
        }
        
        //Check if the server is shutting down.
        if($Event->Name === Event::Shutdown) {
            Log::Info("Relay Client", "Server shut down.");
            parent::Stop();
        }
        
        //Dispatch received Event to EventListeners.
        Log::Info("Relay Client", "Received \"{$Event->Name}\" event.");
        foreach($this->EventListeners->Filter(static fn(EventListener $Listener): bool => $Listener->Event === $Event->Name) as $Listener) {
            $Listener->Handle($Event, $this->Client);
        }
    }
    
    /**
     * @inheritDoc
     */
    public function Stop(int $Code = 0): void {
        $this->Client->Dispatch(new Event(Event::Logout, $this->Client->User->Ticket));
        Log::Info("Relay Client", "Disconnected.");
        parent::Stop($Code);
    }
}