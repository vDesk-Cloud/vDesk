<?php
declare(strict_types=1);

namespace vDesk\Relay\Event;


use vDesk\IO\Socket;

class Listener {

    public function __construct(public string $Event, public string $User, public Socket $Socket) {
    
    }
    
}