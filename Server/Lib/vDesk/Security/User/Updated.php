<?php
declare(strict_types=1);

namespace vDesk\Security\User;

use vDesk\Events\Event;

/**
 * Represents an Event that occurs when an {@link \vDesk\Security\User} has been modified.
 *
 * @package Security
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
class Updated extends Event {

    /**
     * The name of the Event.
     */
    public const Name = "vDesk.Security.User.Updated";

}
