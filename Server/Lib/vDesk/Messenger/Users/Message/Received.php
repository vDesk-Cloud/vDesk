<?php
declare(strict_types=1);

namespace vDesk\Messenger\Users\Message;

/**
 * Event that occurs when a Message has been received by a User.
 *
 * @package vDesk\Messenger
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class Received extends Event {
    
    /**
     * The name of the Event.
     */
    public const Name = "vDesk.Messenger.Users.Message.Received";
    
}