<?php
declare(strict_types=1);

namespace vDesk\Messenger\Users\Message;

/**
 * Event that occurs when a Message has been sent to a User.
 *
 * @package vDesk\Messenger
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class Sent extends Event {
    
    /**
     * The name of the Sent.
     */
    public const Name = "vDesk.Messenger.Users.Message.Sent";
    
}