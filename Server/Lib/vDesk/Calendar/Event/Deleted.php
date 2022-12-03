<?php
declare(strict_types=1);

namespace vDesk\Calendar\Event;

use vDesk\Calendar\Event;
use vDesk\Events\PublicEvent;

/**
 * Event that occurs when an Event has been deleted from the Calendar.
 *
 * @package vDesk\Calendar
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class Deleted extends PublicEvent {
    
    /**
     * The name of the Event.
     */
    public const Name = "vDesk.Calendar.Event.Deleted";

    /**
     * Initializes a new instance of the Deleted Event.
     *
     * @param \vDesk\Calendar\Event $Event Initializes the Event with the specified Event.
     */
    public function __construct(public Event $Event) {
    }
    
    /** @inheritdoc */
    public function ToDataView(): Event {
        return $this->Event;
    }
    
}
