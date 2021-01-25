<?php
declare(strict_types=1);

namespace vDesk\Contacts\Company;

use vDesk\Events\PublicEvent;

/**
 * Represents an Event that occurs when an {@link \vDesk\Contacts\Company} has been deleted from the contacts.
 *
 * @package vDesk\Contacts
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 */
class Deleted extends PublicEvent {
    
    /**
     * The name of the Event.
     */
    public const  Name = "vDesk.Contacts.Company.Deleted";
    
    /**
     * @inheritdoc
     */
    public function ToDataView() {
        return $this->Arguments->ID;
    }
    
}
