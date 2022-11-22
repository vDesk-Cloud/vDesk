<?php
declare(strict_types=1);

namespace vDesk\Security\Group;

use vDesk\Events\PublicEvent;
use vDesk\Security\Group;

/**
 * Event that occurs when a Group has been modified.
 *
 * @package vDesk\Security
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class Updated extends PublicEvent {
    
    /**
     * The name of the Event.
     */
    public const Name = "vDesk.Security.Group.Updated";

    /**
     * Initializes a new instance of the Updated Event.
     *
     * @param \vDesk\Security\Group $Group Initializes the Event with the specified Group.
     */
    public function __construct(public Group $Group) {
    }

    /** @inheritDoc */
    public function ToDataView(): Group {
        return $this->Group;
    }
}
