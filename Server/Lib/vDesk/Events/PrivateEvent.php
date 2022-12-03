<?php
declare(strict_types=1);

namespace vDesk\Events;

use vDesk\DataProvider\Expression;
use vDesk\Security\User;

/**
 * Abstract base class for system-wide dispatchable private Events.
 *
 * @package vDesk\Events
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
abstract class PrivateEvent extends GlobalEvent {
    
    /**
     * Initializes a new instance of the PrivateEvent class.
     *
     * @param \vDesk\Security\User $Receiver  Initializes the PrivateEvent with the specified User that will receive the  Event.
     */
    public function __construct(public User $Receiver) {
    }
    
    /**
     * Saves the PrivateEvent to the database.
     */
    public function Save(): void {
        Expression::Insert()
                  ->Into("Events.Private")
                  ->Values([
                      "TimeStamp " => $this->TimeStamp,
                      "Receiver"   => $this->Receiver,
                      "Name"       => static::Name,
                      "Data"       => $this->ToDataView()
                  ])
                  ->Execute();
    }
    
}
