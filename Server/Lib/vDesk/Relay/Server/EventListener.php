<?php
declare(strict_types=1);

namespace vDesk\Relay\Server;

/**
 * Class that represents a server side EventListener for Relay Events.
 *
 * @package vDesk\Relay
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class EventListener {
    
    /**
     * Initializes a new instance of the EventListener class.
     *
     * @param string                     $Event  Initializes the EventListener with the specified Event.
     * @param \vDesk\Relay\Server\Client $Client Initializes the EventListener with the specified Client.
     */
    public function __construct(public string $Event, public Client $Client) {}
    
}