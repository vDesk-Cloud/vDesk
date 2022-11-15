<?php
declare(strict_types=1);

namespace vDesk\Security\User;

use vDesk\Events\PublicEvent;
use vDesk\Security\User;

/**
 * Event that occurs when a new User has been created.
 *
 * @package vDesk\Security
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class Created extends PublicEvent {

    /**
     * The name of the Event.
     */
    public const Name = "vDesk.Security.User.Created";

    /**
     * Initializes a new instance of the Created Event.
     *
     * @param \vDesk\Security\User $User Initializes the Event with the specified User.
     */
    public function __construct(public User $User) {
    }

    /** @inheritDoc */
    public function ToDataView(): array {
        return ["ID" => $this->User->ID, "Name" => $this->User->Name];
    }

}
