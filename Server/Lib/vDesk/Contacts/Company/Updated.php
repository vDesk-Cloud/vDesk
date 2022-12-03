<?php
declare(strict_types=1);

namespace vDesk\Contacts\Company;

use vDesk\Contacts\Company;
use vDesk\Events\PublicEvent;

/**
 * Event that occurs when the data of a Company has been modified.
 *
 * @package vDesk\Contacts
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class Updated extends PublicEvent {
    
    /**
     * The name of the Event.
     */
    public const Name = "vDesk.Contacts.Company.Updated";

    /**
     * Initializes a new instance of the Updated Event.
     *
     * @param \vDesk\Contacts\Company $Company Initializes the Event with the specified Company.
     */
    public function __construct(public Company $Company) {
    }

    /** @inheritdoc */
    public function ToDataView(): Company {
        return $this->Company;
    }
    
}
