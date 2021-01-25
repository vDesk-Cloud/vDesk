<?php
declare(strict_types=1);

namespace vDesk\Security\User;

use vDesk\Events\Event;

/**
 * Represents an Event that occurs when an {@link \vDesk\Security\User} has been deleted from the system.
 *
 * @package Security
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 */
class Deleted extends Event {
    
    /**
     * The name of the Event.
     */
    public const Name = "vDesk.Security.Group.Deleted";
    
}
