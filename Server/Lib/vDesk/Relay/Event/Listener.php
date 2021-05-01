<?php
declare(strict_types=1);

namespace vDesk\Relay\Event;


use vDesk\IO\Socket;
use vDesk\Relay\Server\Client;

class Listener {

    public function __construct(public string $Event, public Client $Client) {
    
    }
    
}