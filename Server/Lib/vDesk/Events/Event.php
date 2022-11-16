<?php
declare(strict_types=1);

namespace vDesk\Events;

use vDesk\Modules;
use vDesk\Struct\Properties;

/**
 * Abstract base class for dispatchable Events.
 *
 * @package vDesk\Events
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
abstract class Event {

    use Properties;

    /**
     * The name of the Event.
     */
    public const Name = "vDesk.Events.Event";

    /**
     * The creation timestamp of the Event.
     *
     * @var int
     */
    protected int $TimeStamp;

    /**
     * Dispatches the Event to the EventDispatcher.
     */
    final public function Dispatch(): void {
        $this->TimeStamp = \time();
        Modules::Events()::Dispatch($this);
    }

}
