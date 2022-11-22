<?php
declare(strict_types=1);

namespace vDesk\Messenger\Groups\Message;

use vDesk\Events\PrivateEvent;
use vDesk\Messenger\Groups\Message;
use vDesk\Security\User;

/**
 * Event that occurs when a Message has been sent to a Group.
 *
 * @package vDesk\Messenger
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class Sent extends PrivateEvent {

    /**
     * The name of the Event.
     */
    public const Name = "vDesk.Messenger.Groups.Message.Sent";

    /**
     * Initializes a new instance of the Sent Event.
     *
     * @param \vDesk\Security\User            $Receiver Initializes the Event with the specified receiver.
     * @param \vDesk\Messenger\Groups\Message $Message  Initializes the Event with the specified read Message.
     */
    public function __construct(User $Receiver, public Message $Message) {
        parent::__construct($Receiver);
    }

    /** @inheritDoc */
    public function ToDataView(): array {
        return [
            "ID"     => $this->Message->ID,
            "Sender" => $this->Message->Sender->ID,
            "Group"  => $this->Message->Group->ID
        ];
    }

}