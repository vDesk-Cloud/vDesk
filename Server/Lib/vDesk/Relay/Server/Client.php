<?php
declare(strict_types=1);

namespace vDesk\Relay\Server;

use vDesk\IO\Socket;
use vDesk\Security\User;

/**
 * Class that represents a container object for client connections.
 *
 * Sorry, neither
 *      $SadLiteralNoises = new \stdClass();
 *      $SadLiteralNoises->User = $User;
 *      $this->Clients->Add($SadLiteralNoises);
 * nor
 *      $this->Clients->Add((object)["Socket" => $Socket]);
 * is cool.
 *
 * C'mon PHP-devs, arrays have such a nice syntax; I beg you: please give us finally object literals!
 *
 * @package vDesk\Relay
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class Client {
    /**
     * Initializes a new instance of the Client class.
     *
     * @param \vDesk\Security\User $User   Initializes the Client with the specified User.
     * @param \vDesk\IO\Socket     $Socket Initializes the Client with the specified Socket.
     */
    public function __construct(public User $User, public Socket $Socket) {}
}