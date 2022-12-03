<?php
declare(strict_types=1);

namespace vDesk\Security\Group;

use vDesk\Events\PublicEvent;
use vDesk\Security\Group;

/**
 * Event that occurs when a new Group has been created.
 *
 * @package vDesk\Security
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class Created extends PublicEvent {
    
    /**
     * The name of the Event.
     */
    public const Name = "vDesk.Security.Group.Created";

    /**
     * Initializes a new instance of the Created Event.
     *
     * @param \vDesk\Security\Group $Group Initializes the Event with the specified Group.
     */
    public function __construct(public Group $Group) {
    }

    /** @inheritDoc */
    public function ToDataView(): array {
        return ["ID" => $this->Group->ID, "Name" => $this->Group->Name];
    }
}
