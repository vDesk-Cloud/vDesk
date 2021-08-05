<?php
declare(strict_types=1);

namespace vDesk\Relay\Client;

use vDesk\Relay\API\Client;
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
     * @param string   $Event    Initializes the EventListener with the specified Event.
     * @param callable $Callback Initializes the EventListener with the specified callback to execute if the specified Event has been dispatched.
     */
    public function __construct(public string $Event, callable $Callback) {
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