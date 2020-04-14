<?php
declare(strict_types=1);

namespace vDesk\Security\Group;

use vDesk\Events\PublicEvent;

/**
 * Represents an Event that occurs when an {@link \vDesk\Security\Group} has been added to the system.
 *
 * @package vDesk\Security
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
class Added extends PublicEvent {

    /**
     * The name of the Event.
     *
     * @var string
     */
    public const Name = "vDesk.Security.Group.Created";

    /**
     * Gets a string-representation of the arguments of the IGlobalEvent.
     *
     * @return string A string-representation of the arguments of the IGlobalEvent.
     */
    public function ToDataView(): string {
        // TODO: Implement GetData() method.
    }
}
