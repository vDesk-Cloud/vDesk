<?php
declare(strict_types=1);

namespace vDesk\Events;

use vDesk\Modules;
use vDesk\Struct\Properties;

/**
 * Abstract base class for dispatchable Events.
 *
 * @property-read string $Name      Gets the name of the Event.
 * @property-read mixed  $Sender    Gets the instance of the class which raised the Event.
 * @property-read mixed  $Arguments Gets the arguments of the Event.
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
        Modules::EventDispatcher()::Dispatch($this);
    }

}
