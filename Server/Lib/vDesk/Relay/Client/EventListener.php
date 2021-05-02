<?php
declare(strict_types=1);

namespace vDesk\Relay\Client;

use vDesk\Relay\Client;
use vDesk\Relay\Event;

/**
 * Class that represents a client side EventListener for Relay Events.
 *
 * @package vDesk\Relay
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class EventListener {
    
    /**
     * The callback of the EventListener.
     *
     * @var callable|null
     */
    private $Callback;
    
    /**
     * Creates a new instance of the EventListener class.
     *
     * @param string              $Event    Initializes the EventListener with the specified Event.
     * @param \vDesk\Relay\Client $Client   Initializes the EventListener with the specified Client.
     * @param callable            $Callback The callback to execute when an Event with the specified name has been occurred.
     */
    public function __construct(public string $Event, public Client $Client, callable $Callback) {
        $this->Callback = $Callback;
    }
    
    /**
     * Executes a registered EventListener callback.
     *
     * @param \vDesk\Relay\Event $Event The Dispatched Event.
     */
    public function Handle(Event $Event, Client $Client): void {
        $Callback = $this->Callback;
        $Callback($Event, $Client);
    }
}