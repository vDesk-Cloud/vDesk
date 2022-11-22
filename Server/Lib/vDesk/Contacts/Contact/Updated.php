<?php
declare(strict_types=1);

namespace vDesk\Contacts\Contact;

use vDesk\Contacts\Contact;
use vDesk\Events\PublicEvent;

/**
 * Event that occurs when the data of a Contact has been modified.
 *
 * @package vDesk\Contacts
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class Updated extends PublicEvent {
    
    /**
     * The name of the Event.
     */
    public const Name = "vDesk.Contacts.Contact.Updated";

    /**
     * Initializes a new instance of the Updated Event.
     *
     * @param \vDesk\Contacts\Contact $Contact Initializes the Event with the specified Contact.
     */
    public function __construct(public Contact $Contact) {
    }

    /** @inheritdoc */
    public function ToDataView(): Contact {
        return $this->Contact;
    }
    
}
