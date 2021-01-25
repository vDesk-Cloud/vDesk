<?php
declare(strict_types=1);

namespace vDesk\Calendar\Event;

use vDesk\Events\PublicEvent;

/**
 * Represents an Event that occurs when the data of an {@link \vDesk\Calendar\Event} has been modified.
 *
 * @package vDesk\Calendar
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 */
class Updated extends PublicEvent {
    
    /**
     * The name of the Event.
     */
    public const Name = "vDesk.Calendar.Event.Updated";
    
    /**
     * @inheritdoc
     */
    public function ToDataView(): string {
        return (string)$this->Arguments->ID;
    }
    
}
