<?php
declare(strict_types=1);

namespace vDesk\Messenger\Groups\Message;

use vDesk\DataProvider\Expression;
use vDesk\Events\PrivateEvent;
use vDesk\Messenger\Groups\Message;
use vDesk\Security\User;

/**
 * Event fired if a new Message has been sent to a Group.
 *
 * @package vDesk\Messenger\Users\Message
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class Sent extends PrivateEvent {
    
    /**
     * The name of the Sent.
     */
    public const Name = "vDesk.Messenger.Groups.Message.Sent";
    
    /**
     * Initializes a new instance of the Read class.
     *
     * @param \vDesk\Security\User            $Receiver  Initializes the Event with the specified receiver.
     * @param \vDesk\Messenger\Groups\Message $Arguments Initializes the Event with the specified read Message.
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
                          "ID"     => $this->Arguments->ID,
                          "Sender" => $this->Arguments->Sender->ID,
                          "Group"  => $this->Arguments->Group->ID
                      ]
                  ])
                  ->Execute();
        
    }
}