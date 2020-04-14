<?php
declare(strict_types=1);

namespace vDesk\Events;

use vDesk\DataProvider\Expression;

/**
 * Represents the base-class for dispatchable public Events.
 *
 * @property-read string $Name      Gets the name of the GlobalEvent.
 * @property-read mixed  $Sender    Gets the instance of the class which raised the GlobalEvent.
 * @property-read mixed  $Arguments Gets the arguments of the GlobalEvent.
 * @package vDesk\Events
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
abstract class PublicEvent extends GlobalEvent {

    /**
     * Saves the PublicEvent to the database.
     */
    public function Save(): void {
        Expression::Insert()
                  ->Into("Events.Public")
                  ->Values([
                      "TimeStamp " => $this->TimeStamp,
                      "Name"       => static::Name,
                      "Data"       => $this->Arguments
                  ])
                  ->Execute();
    }

}
