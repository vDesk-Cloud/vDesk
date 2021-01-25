<?php
declare(strict_types=1);

namespace vDesk\Calendar\Event;

use vDesk\Events\PublicEvent;

/**
 * Represents an Event that occurs when an {@link \vDesk\Calendar\Event} has been deleted from the calendar.
 *
 * @package vDesk\Calendar
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 */
class Deleted extends PublicEvent {
    
    /**
     * The name of the Event.
     */
    public const Name = "vDesk.Calendar.Event.Deleted";
    
    /**
     * @inheritdoc
     */
    public function ToDataView(): string {
        return (string)$this->Arguments->ID;
    }
    
}
