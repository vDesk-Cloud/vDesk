<?php
declare(strict_types=1);

namespace vDesk\Security\User;

use vDesk\Events\PublicEvent;
use vDesk\Security\User;

/**
 * Event that occurs when an User has been deleted.
 *
 * @package vDesk\Security
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class Deleted extends PublicEvent {
    
    /**
     * The name of the Event.
     */
    public const Name = "vDesk.Security.User.Deleted";

    /**
     * Initializes a new instance of the Deleted Event.
     *
     * @param \vDesk\Security\User $User Initializes the Event with the specified User.
     */
    public function __construct(public User $User) {
    }

    /** @inheritDoc */
    public function ToDataView(): User {
        return $this->User;
    }
    
}
