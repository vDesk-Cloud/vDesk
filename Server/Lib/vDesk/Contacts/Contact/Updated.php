<?php
declare(strict_types=1);

namespace vDesk\Contacts\Contact;

use vDesk\Events\PublicEvent;

/**
 * Represents an Event that occurs when the data of an {@link \vDesk\Contacts\Contact} has been modified.
 *
 * @package Contacts
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
class Updated extends PublicEvent {

    /**
     * The name of the Event.
     */
    public const Name = "vDesk.Contacts.Contact.Updated";

    /**
     * @inheritdoc
     */
    public function ToDataView() {
        return $this->Arguments->ID;
    }

}
