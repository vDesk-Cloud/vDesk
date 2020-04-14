<?php
declare(strict_types=1);

namespace vDesk\Contacts\Company;

use vDesk\Events\PublicEvent;

/**
 * Represents an Event that occurs when the data of an {@link \vDesk\Contacts\Company} has been modified.
 *
 * @package Contacts
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
class Updated extends PublicEvent {

    /**
     * The name of the Event.
     */
    public const Name = "vDesk.Contacts.Company.Updated";

    /**
     * @inheritdoc
     */
    public function ToDataView() {
        return $this->Arguments->ID;
    }

}
