<?php
declare(strict_types=1);

namespace vDesk\Messenger\Users\Message;

/**
 * Represents an Event that occurs when a {@link \vDesk\Messenger\Message} has been read.
 *
 * @package vDesk\Messenger\Message
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 * @version 1.0.0
 */
class Read extends Event {

    /**
     * The name of the Event.
     */
    public const Name = "vDesk.Messenger.Users.Message.Read";

}