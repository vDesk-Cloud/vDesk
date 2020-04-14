<?php
declare(strict_types=1);

namespace vDesk\Security\User;

use vDesk\Events\PublicEvent;

/**
 * Represents an Event that occurs when an {@link \vDesk\Security\User} has been added to the system.
 *
 * @package Security
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
class Added extends PublicEvent {

    /**
     * The name of the User\Created-Event.
     */
    public const Name = "vDesk.Security.User.Created";

    /**
     * Gets a string-representation of the arguments of the IGlobalEvent.
     *
     * @return string A string-representation of the arguments of the IGlobalEvent.
     */
    public function ToDataView(): string {
        return \json_encode($this->Arguments->ToDataView());
    }
}
