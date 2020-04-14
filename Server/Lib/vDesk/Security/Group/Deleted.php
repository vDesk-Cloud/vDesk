<?php
declare(strict_types=1);

namespace vDesk\Security\Group;

use vDesk\Events\Event;

/**
 * Represents an Event that occurs when an {@link \vDesk\Security\Group} has been deleted from the system.
 *
 * @package vDesk\Security
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
class Deleted extends Event {

    /**
     * The name of the Group\Deleted-Event.
     */
    public const Name = "vDesk.Security.Group.Deleted";

}
