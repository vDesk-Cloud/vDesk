<?php
declare(strict_types=1);

namespace vDesk\Contacts\Company;

use vDesk\Events\PublicEvent;
use vDesk\Contacts\Company;

/**
 * Event that occurs when a new Company has been added to the Contacts.
 *
 * @package vDesk\Contacts
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class Created extends PublicEvent {

    /**
     * The name of the Event.
     */
    public const Name = "vDesk.Contacts.Company.Created";

    /**
     * Initializes a new instance of the Created Event.
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
