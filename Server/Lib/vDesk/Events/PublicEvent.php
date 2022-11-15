<?php
declare(strict_types=1);

namespace vDesk\Events;

use vDesk\DataProvider\Expression;

/**
 * Abstract base class for system-wide dispatchable public Events.
 *
 * @package vDesk\Events
 * @author  Kerry <DevelopmentHero@gmail.com>
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
                      "Data"       => $this->ToDataView()
                  ])
                  ->Execute();
    }
    
}
