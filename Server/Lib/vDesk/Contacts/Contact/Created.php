<?php
declare(strict_types=1);

namespace vDesk\Contacts\Contact;

use vDesk\Events\PublicEvent;

/**
 * Represents an Event that occurs when a new {@link \vDesk\Contacts\Contact} has been added to the contacts.
 *
 * @package vDesk\Contacts
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class Created extends PublicEvent {
    
    /**
     * The name of the Event.
     */
    public const Name = "vDesk.Contacts.Contact.Created";
    
    /**
     * @inheritdoc
     */
    public function ToDataView() {
        return $this->Arguments->ID;
    }
    
}
