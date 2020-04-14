<?php
declare(strict_types=1);

namespace vDesk\Messenger\Users\Message;

use vDesk\DataProvider\Expression;
use vDesk\Events\PrivateEvent;
use vDesk\Messenger\Users\Message;
use vDesk\Security\User;

/**
 * Class that represents a private Message Event.
 *
 * @package vDesk\Messenger\Users\Message
 * @author  Kerry <DevelopmentHero@gmail.com>
 * @version 1.0.0
 */
abstract class Event extends PrivateEvent {
    
    /**
     * Initializes a new instance of the Read class.
     *
     * @param \vDesk\Security\User           $Receiver  Initializes the Event with the specified receiver.
     * @param \vDesk\Messenger\Users\Message $Arguments Initializes the Event with the specified read Message.
     */
    public function __construct(User $Receiver, Message $Arguments = null) {
        parent::__construct($Receiver, $Arguments);
    }
    
    /**
     * Saves the Received to the database.
     */
    public function Save(): void {
        Expression::Insert()
                  ->Into("Events.Private")
                  ->Values([
                      "TimeStamp " => $this->TimeStamp,
                      "Receiver"   => $this->Receiver,
                      "Name"       => static::Name,
                      "Data"       => [
                          "ID"        => $this->Arguments->ID,
                          "Sender"    => $this->Arguments->Sender->ID,
                          "Recipient" => $this->Arguments->Recipient->ID
                      ]
                  ])
                  ->Execute();
        
    }
}