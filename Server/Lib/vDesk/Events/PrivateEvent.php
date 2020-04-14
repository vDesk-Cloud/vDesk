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
 * @version 1.0.0.
 */
abstract class PrivateEvent extends GlobalEvent {

    /**
     * The ID of the receiving User.
     *
     * @var \vDesk\Security\User
     */
    protected User $Receiver;

    /**
     * Initializes a new instance of the PrivateEvent class.
     *
     * @param \vDesk\Security\User $Receiver  The User that will receive the PrivateEvent.
     * @param mixed                $Arguments The arguments of the PrivateEvent.
     */
    public function __construct(User $Receiver, $Arguments = null) {
        parent::__construct($Arguments);
        $this->Receiver = $Receiver;
        $this->AddProperty("Receiver", [\Get => fn(): ?int => $this->Receiver]);
    }

    /**
     * Saves the PrivateEvent to the database.
     */
    public function Save(): void {
        Expression::Insert()
                  ->Into("Events.Private")
                  ->Values([
                      "TimeStamp " => $this->TimeStamp,
                      "Receiver" => $this->Receiver,
                      "Name"     => static::Name,
                      "Data"     => $this->Arguments
                  ])
                  ->Execute();
    }

}
