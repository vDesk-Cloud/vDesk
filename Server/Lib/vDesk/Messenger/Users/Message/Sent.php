<?php
declare(strict_types=1);

namespace vDesk\Messenger\Users\Message;

/**
 * Represents an Event that occurs when a new {@link \vDesk\Messenger\Users\Message} has been sent.
 *
 * @package vDesk\Messenger\Message
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 * @version 1.0.0
 */
class Sent extends Event {

    /**
     * The name of the Sent.
     */
    public const Name = "vDesk.Messenger.Users.Message.Sent";

}