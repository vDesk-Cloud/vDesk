<?php
declare(strict_types=1);

namespace vDesk\Calendar\Event;

use vDesk\Calendar\Event;
use vDesk\Events\PublicEvent;

/**
 * Event that occurs when an Event has been added to the Calendar.
 *
 * @package vDesk\Calendar
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class Created extends PublicEvent {

    /**
     * The name of the Event.
     */
    public const Name = "vDesk.Calendar.Event.Created";

    /**
     * Initializes a new instance of the Created Event.
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
