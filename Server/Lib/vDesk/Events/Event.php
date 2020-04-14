<?php
declare(strict_types=1);

namespace vDesk\Events;

use vDesk\Modules;
use vDesk\Struct\Properties;

/**
 * Represents the base-class for dispatchable Events.
 *
 * @property-read string $Name      Gets the name of the Event.
 * @property-read mixed  $Sender    Gets the instance of the class which raised the Event.
 * @property-read mixed  $Arguments Gets the arguments of the Event.
 * @package vDesk\Events
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
abstract class Event {

    use Properties;

    /**
     * The name of the Event.
     */
    public const Name = "vDesk.Events.Event";

    /**
     * The arguments of the Event.
     *
     * @var mixed
     */
    protected $Arguments;

    /**
     * The timestamp the Event has occurred.
     *
     * @var int
     */
    protected int $TimeStamp;

    /**
     * Initializes a new instance of the Event class.
     *
     * @param mixed $Arguments The arguments of the Event.
     */
    public function __construct($Arguments) {
        $this->Arguments = $Arguments;
        $this->TimeStamp = \time();
        $this->AddProperties([
            "Sender"    => [\Get => fn() => $this->Sender],
            "Arguments" => [\Get => fn() => $this->Arguments],
            "TimeStamp" => [\Get => fn(): int => $this->TimeStamp]
        ]);
    }

    /**
     * Dispatches the Event to the EventDispatcher.
     */
    final public function Dispatch(): void {
        Modules::EventDispatcher()::Dispatch($this);
    }

}
