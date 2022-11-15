<?php
declare(strict_types=1);

namespace vDesk\Messenger\Users\Message;

use vDesk\Events\PrivateEvent;
use vDesk\Messenger\Users\Message;
use vDesk\Security\User;

/**
 * Abstract base class for User Message Events.
 *
 * @package vDesk\Messenger\Users\Message
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
abstract class Event extends PrivateEvent {

    /**
     * Initializes a new instance of the Message Event.
     *
     * @param \vDesk\Security\User           $Receiver Initializes the Event with the specified receiver.
     * @param \vDesk\Messenger\Users\Message $Message  Initializes the Event with the specified read Message.
     */
    public function __construct(User $Receiver, public Message $Message) {
        parent::__construct($Receiver);
    }

    /** @inheritDoc */
    public function ToDataView(): array {
        return [
            "ID"        => $this->Message->ID,
            "Sender"    => $this->Message->Sender->ID,
            "Recipient" => $this->Message->Recipient->ID
        ];
    }
}