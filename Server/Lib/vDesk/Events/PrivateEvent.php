<?php
declare(strict_types=1);

namespace vDesk\Events;

use vDesk\DataProvider\Expression;
use vDesk\Security\User;

/**
 * Represents the base-class for system-wide dispatchable private Events.
 *
 * @property-read int $Receiver Gets the ID of the receiving User of the PrivateEvent.
 * @package vDesk\Events
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 */
abstract class PrivateEvent extends GlobalEvent {
    
    /**
     * Initializes a new instance of the PrivateEvent class.
     *
     * @param \vDesk\Security\User $Receiver  The User that will receive the PrivateEvent.
     * @param mixed                $Arguments The arguments of the PrivateEvent.
     */
    public function __construct(protected User $Receiver, protected mixed $Arguments = null) {
        parent::__construct($Arguments);
        $this->AddProperty("Receiver", [\Get => fn(): User => $Receiver]);
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
                      "Data"       => $this->Arguments
                  ])
                  ->Execute();
    }
    
}
